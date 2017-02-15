<?php
/**
 * Created by PhpStorm.
 * User: josue
 * Date: 2/14/17
 * Time: 6:29 PM
 */

namespace App\Http\Controllers;

use App\EmployeeChecklist;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class EmployeeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function checklist($id){
        $user = Auth::user();
        if(!$user->hasRole('admin')){
            if($user->id != $id){
                abort(403);
            }
        }
        return view('employees.checklist')->with('id',$id);
    }
    public function getChecklist($id){
        try{
            $user = User::find($id);
            if(!$user->hasRole('employee')){
                throw new Exception("The user {$user->name} doesn't have Employee role assigned.");
            }
            $uploadTypes = config('checklist.upload-types');
            if(!isset($uploadTypes)) throw new Exception("variable 'checklist.upload-types' not set.");
            $checklist = $user->employeeChecklist;
            if($checklist == null){
                $checklist = new EmployeeChecklist();
                $checklist->user_id = $id;
                $checklist->uploads = [];
            }
            $uploads = $checklist->uploads;
            foreach($uploadTypes as $uploadType){
                foreach($checklist->uploads as $upload){
                    if($upload["type"]==$uploadType){
                        continue 2;
                    }
                }
                array_push($uploads,["type"=>$uploadType]);
            }
            $checklist->uploads = $uploads;
            $checklist->save();
            $response = ["success"=>true, "checklist"=>$checklist];
        }catch(Exception $ex) {
            $response = ["success"=>false, "message"=>$ex->getMessage()];
        }
        return response()->json($response);
    }
    public function saveChecklist(Request $request){
        try{
            $data = $request->input();
            $checklist = EmployeeChecklist::find($data["id"]);
            $checklist->fill($data);
            $checklist->save();
            $response = ["success"=>true];
        }catch(Exception $ex) {
            $response = ["success"=>false, "message"=>$ex->getMessage()];
        }
        return response()->json($response);
    }
    public function uploadChecklistFile(Request $request){
        try{
            $data = $request->input();
            $checklist = EmployeeChecklist::find($data['id']);
            if($checklist == null) throw new Exception("Employee Checklist not found.");
            $file = $request->file('file');
            $file_name = $file->getClientOriginalName();
            $rand = rand(1111,9999);
            $newFileName="{$rand}-{$file_name}";
            if (!file_exists(public_path().'/uploads')) {
                mkdir(public_path().'/uploads',0777, true);
            }
            $file->move(public_path("/uploads"), $newFileName);
            $uploads = $checklist->uploads;
            foreach($uploads as $key => $upload){
                if($upload["type"] == $data['uploadType']){
                    $uploads[$key]["fileName"]=$newFileName;
                }
            }
            $checklist->uploads = $uploads;
            $checklist->save();
            $response = ["success"=>true, "fileName"=>$newFileName, "status"=>$checklist->status];
        }catch(Exception $ex) {
            $response = ["success"=>false, "message"=>$ex->getMessage()];
        }
        return response()->json($response);
    }
    public function deleteUploadedChecklistFile(Request $request){
        try{
            $data = $request->input();
            $checklist = EmployeeChecklist::find($data['id']);
            if($checklist == null) throw new Exception("Employee Checklist not found.");
            $uploads = $checklist->uploads;
            foreach($uploads as $key => $upload){
                if($upload["type"]==$data["type"]){
                    $file_path = public_path().'/uploads/'.$upload["fileName"];
                    if(file_exists($file_path)){
                        unlink($file_path);
                    }
                    unset($uploads[$key]["fileName"]);
                    break;
                }
            }

            $checklist->uploads = $uploads;
            $checklist->save();
            $response = ["success"=>true, "type"=>$data["type"], "status"=>$checklist->status];
        }catch(Exception $ex){
            $response = ["success"=>false, "message"=>$ex->getMessage()];
        }
        return response()->json($response);
    }
}
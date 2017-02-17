<?php
/**
 * Created by PhpStorm.
 * User: josue
 * Date: 2/14/17
 * Time: 6:29 PM
 */

namespace App\Http\Controllers;


use App\Role;
use App\User;
use Illuminate\Http\Request;
use Exception;

use HTML;
use Form;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Nayjest\Grids\Components\ColumnHeadersRow;
use Nayjest\Grids\Components\FiltersRow;
use Nayjest\Grids\Components\HtmlTag;
use Nayjest\Grids\Components\Laravel5\Pager;
use Nayjest\Grids\Components\OneCellRow;
use Nayjest\Grids\Components\ShowingRecords;
use Nayjest\Grids\Components\TFoot;
use Nayjest\Grids\Components\THead;
use Nayjest\Grids\EloquentDataProvider;
use Nayjest\Grids\FieldConfig;
use Nayjest\Grids\FilterConfig;
use Nayjest\Grids\Grid;
use Nayjest\Grids\GridConfig;


class EmployeeController extends Controller
{

    public function index()
    {

        $role = Role::where('name', 'employee')->first();
        if($role == null) throw new Exception('Employee role not found');
        $cfg = (new GridConfig())
            ->setDataProvider(
                new EloquentDataProvider(
                    (new User())->newQuery()->whereExists(function($query){
                        $role = Role::where('name', 'employee')->first();
                        $query->select(DB::raw(1))
                            ->from('role_user')
                            ->whereRaw("role_user.user_id = users.id AND role_user.role_id = {$role->id}");
                    })
                )
            )
            ->setName('employees_grid')
            ->setColumns([
                (new FieldConfig)
                    ->setName('id')
                    ->setLabel('ID')
                    ->setSortable(true)
                    ->setSorting(Grid::SORT_ASC),
                (new FieldConfig)
                    ->setName('name')
                    ->setLabel('Name')
                    ->setSortable(true)
                    ->setCallback(function ($val, $row) {
                        $model = $row->getSrc();
                        return $model->name;
                    }),
                (new FieldConfig)
                    ->setName('email')
                    ->setSortable(true)
                    ->setCallback(function ($val) {
                        if(empty($val)) return "";
                        $icon = '<span class="glyphicon glyphicon-envelope"></span>';
                        $icon = HTML::decode(HTML::link("mailto:$val", $icon));
                        return
                            $icon." ".HTML::link("mailto:$val", $val);
                    }),
                (new FieldConfig)
                    ->setName('status')
                    ->setLabel('Checklist Status')
                    ->setCallback(function ($val, $row) {
                        $model = $row->getSrc();
                        if(($model->employeeChecklist) && $model->employeeChecklist->status == "complete")
                            return "<span class=\"bg-success\" style=\"color: white;\">&nbsp;Complete&nbsp;</span>";
                        return "<span class=\"bg-danger\" style=\"color: white;\">&nbsp;Incomplete&nbsp;</span>";

                    }),
                (new FieldConfig)
                    ->setName('actions')
                    ->setLabel(' ')
                    ->setCallback(function($val, $row){
                        $model = $row->getSrc();
                        $buttons =
                            "<div class='btn-group'>
                                <a href='". route('employees.edit', [$model->id]) ."' class='btn btn-primary btn-xs' title='Login Data'><i class='glyphicon glyphicon-user'></i></a>
                                <a href='". route('employees.checklist', [$model->id, 'employees']) ."' class='btn btn-default btn-xs' title='Checklist'><i class='glyphicon glyphicon-check'></i></a>
                                <a href='". route('employees.destroy', [$model->id]) ."' title='Delete' data-delete=''  class='btn btn-danger btn-xs'><i class='glyphicon glyphicon-trash'></i></a>
                            </div>";
                        return $buttons;
                    })
            ])
            ->setComponents([
                (new THead)
                    ->setComponents([
                        (new ColumnHeadersRow),
                        (new FiltersRow)
                    ]),
                (new TFoot)
                    ->setComponents([
                        (new OneCellRow)
                            ->setComponents([
                                new Pager,
                                (new HtmlTag)
                                    ->setAttributes(['class' => 'pull-right'])
                                    ->addComponent(new ShowingRecords),
                            ])
                    ])
            ])
        ;
        $grid = (new Grid($cfg))->render();

        return view('employees.index', compact('grid'));
    }
    public function edit($id){
        try{
            $user = $this->loadModel($id);
            return view('employees.edit')->with('employee', $user);
        }catch(Exception $ex) {
            Session::flash('error', $ex->getMessage());
            return redirect(route('employees.index'));
        }
    }
    public function update(Request $request, $id){
        try{
            $user = $this->loadModel($id);
            $this->validate($request, [
                'name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:users,email,'.$id,
                'password'=>'min:6|confirmed',
            ]);
            $input = $request->all();
            if(empty($input["password"])){
                unset($input["password"]);
            }else{
                $input["password"] = bcrypt($input["password"]);
            }
            $user->update($input);
            Session::flash('success','Employee updated successfully.');
            return redirect(route('employees.index'));
        }catch(Exception $ex){
            Session::flash('error',$ex->getMessage());
            return redirect(route('employees.index'));
        }
    }
    public function destroy($id)
    {
        try{
            $user = $this->loadModel($id);
            $user->delete();
            Session::flash('success','Employee deleted successfully.');
        }catch(Exception $ex){
            Session::flash('error', $ex->getMessage());
        }
        return redirect(route('employees.index'));
    }

    private function loadModel($id){
        $user = User::find($id);
        if(empty($user)) {
            throw new Exception('User not found');
        }
        if(!$user->hasRole('employee')){
            throw new Exception('User is not an employee');
        }
        return $user;
    }


}
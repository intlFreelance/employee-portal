<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeChecklist extends Model
{
    protected $fillable = ['id','user_id', 'firstName', 'lastName', 'address', 'uploads', 'status'];
    protected $casts = [
        'address' => 'array',
        'uploads'=>'array'
    ];
    public $timestamps = false;

    public function employee(){
        return $this->belongsTo('App\User', 'user_id');
    }
    public function save(array $options = [])
    {
        $this->checkStatus();
        return parent::save($options);
    }

    public function checkStatus(){
        $this->status = "incomplete";
        if(!empty($this->firstName) && !empty($this->lastName)){
            if(!empty($this->address["line1"]) && !empty($this->address["city"]) && !empty($this->address["state"]) && !empty($this->address["zip"])){
                $complete = true;
                foreach($this->uploads as $upload){
                    if(empty($upload["fileName"])){
                        $complete = false;
                        break;
                    }
                }
                if($complete){
                    $this->status = "complete";
                }
            }
        }
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $guarded = ['id'];

    public function User(){
      return $this->hasOne('App\User');
    }

       public function Reimbursements(){
      return $this->hasMany('App\Reimbursement','employee_id');
    }



}

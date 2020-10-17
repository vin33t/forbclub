<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reimbursement extends Model
{
    protected $guarded = ['id'];
    public function Employee(){
      return $this->belongsTo('App\Employee','employee_id');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    protected $guarded = ['id'];

    public function User(){
      return $this->belongsTo('App\User');
    }
}

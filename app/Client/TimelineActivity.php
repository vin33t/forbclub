<?php

namespace App\Client;

use Illuminate\Database\Eloquent\Model;

class TimelineActivity extends Model
{
    protected $guarded = ['id'];

    public function Comments(){
      return $this->hasMany('App\Client\TimelineActivityComments')->whereNull('parent_id');
    }
    public function User(){
      return $this->belongsTo('App\User');
    }
}

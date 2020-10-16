<?php

namespace App\Client;

use Illuminate\Database\Eloquent\Model;

class TimelineActivityComments extends Model
{
    protected $guarded = ['id'];

  public function User(){
    return $this->belongsTo('App\User');
  }
}

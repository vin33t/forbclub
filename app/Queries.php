<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Queries extends Model
{
    protected $guarded = ['id'];

    public function Client(){
    return $this->belongsTo('App\Client\Client','clientId');
  }
}

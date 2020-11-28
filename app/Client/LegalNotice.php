<?php

namespace App\Client;

use Illuminate\Database\Eloquent\Model;

class LegalNotice extends Model
{
    protected $guarded = ['id'];

    public function Client(){
      $this->belongsTo('App\Client\Client');
    }
}

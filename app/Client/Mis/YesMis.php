<?php

namespace App\Client\Mis;

use Illuminate\Database\Eloquent\Model;

class YesMis extends Model
{
    protected $guarded = ['id'];

    public function Client(){
        return $this->belongsTo('App\Client\Client');
    }
}

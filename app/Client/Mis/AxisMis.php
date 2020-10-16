<?php

namespace App\Client\Mis;

use Illuminate\Database\Eloquent\Model;

class AxisMis extends Model
{
    protected $guarded = ['id'];

    public function client(){
        return $this->belongsTo('App\Client\Client');
    }
}

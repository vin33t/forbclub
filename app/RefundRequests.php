<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RefundRequests extends Model
{
  protected $guarded = ['id'];


  public function client(){
    return $this->belongsTo('App\Client\Client');
  }
}

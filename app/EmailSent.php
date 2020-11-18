<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailSent extends Model
{
  protected  $guarded = ['id'];

  public function mail(){
    return $this->belongsTo('App\Emails');
  }
}

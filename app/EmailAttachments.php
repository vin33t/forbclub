<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailAttachments extends Model
{
  protected  $guarded = ['id'];

  public function mail(){
    return $this->belongsTo('App\Emails');
  }
}

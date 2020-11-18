<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Emails extends Model
{
  protected $guarded = ['id'];

  public function attachments(){
    return $this->hasMany('App\EmailAttachments', 'email_id');
  }
  public function reply(){
    return $this->hasMany('App\EmailSent');
  }
  public function client(){
    return $this->belongsTo('App\Client\Client');
  }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientDocuments extends Model
{
    protected $guarded = ['id'];

    public function Client(){
      return $this->belongsTo('App\Client\Client','client_id');
    }
}

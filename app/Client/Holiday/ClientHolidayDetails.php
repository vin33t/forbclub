<?php

namespace App\Client\Holiday;

use Illuminate\Database\Eloquent\Model;

class ClientHolidayDetails extends Model
{
  protected $guarded = ['id'];


  public function ClientHoliday(){
        return $this->belongsTo('App\Client\Holiday\ClientHoliday');
    }

    public function ClientHolidayTransactions(){
        return $this->hasMany('App\Client\Holiday\ClientHolidayTransactions');
    }
}

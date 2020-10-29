<?php

namespace App\Client\Holiday;

use Illuminate\Database\Eloquent\Model;

class ClientHoliday extends Model
{
    protected $guarded = ['id'];

    public function Booking(){
        return $this->belongsTo('App\Client\Booking\Bookings');
    }

    public function Client(){
        return $this->belongsTo('App\Client\Client');
    }

    public function ClientHolidayDetails(){
        return $this->hasMany('App\Client\Holiday\ClientHolidayDetails');
    }
}

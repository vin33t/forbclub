<?php

namespace App\Client\Booking;

use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
    protected $guarded = ['id'];

    public function BookingInfo(){
      return $this->hasMany('App\Client\Booking\BookingInfo','bookings_id');
    }
}

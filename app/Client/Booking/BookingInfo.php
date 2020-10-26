<?php

namespace App\Client\Booking;

use Illuminate\Database\Eloquent\Model;

class BookingInfo extends Model
{
    protected $guarded = ['id'];

    public function Booking(){
      return$this->belongsTo('App\Client\Booking\Bookings','bookings_id');
    }
}

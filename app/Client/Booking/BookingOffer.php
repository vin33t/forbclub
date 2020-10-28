<?php

namespace App\Client\Booking;

use Illuminate\Database\Eloquent\Model;

class BookingOffer extends Model
{
    protected $guarded = ['id'];

    public function Booking(){
      return $this->belongsTo('App\Client\Booking\Bookings','bookings_id');
    }

  public function BookingOfferInfo(){
    return $this->hasMany('App\Client\Booking\BookingOfferInfo','booking_offer_id');
  }
}

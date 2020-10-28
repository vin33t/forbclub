<?php

namespace App\Client\Booking;

use Illuminate\Database\Eloquent\Model;

class BookingOfferInfo extends Model
{
    protected $guarded = ['id'];

  public function BookingOffer(){
    return $this->belongsTo('App\Client\Booking\BookingOffer', 'booking_offer_id');
  }
}

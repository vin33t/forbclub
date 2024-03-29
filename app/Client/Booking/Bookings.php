<?php

namespace App\Client\Booking;

use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
    protected $guarded = ['id'];

    public function BookingInfo(){
      return $this->hasMany('App\Client\Booking\BookingInfo','bookings_id');
    }

    public function Client(){
      return $this->belongsTo('App\Client\Client','clientId');
    }

    public function Employee(){
      return $this->belongsTo('App\User','addedBy');
    }

    public function BookingOffer(){
      return$this->hasOne('App\Client\Booking\BookingOffer','bookings_id');
    }

  public function ClientHoliday(){
    return $this->hasOne('App\Client\Holiday\ClientHoliday');
  }
}

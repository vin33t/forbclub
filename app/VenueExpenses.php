<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VenueExpenses extends Model
{
    protected $guarded = ['id'];

    public function Venue(){
      return $this->belongsTo('App\Venue','venue_id');
    }
}

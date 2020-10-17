<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    protected $guarded = ['id'];

    public function Expense(){
      return $this->hasMany('App\VenueExpenses','venue_id');
    }

}

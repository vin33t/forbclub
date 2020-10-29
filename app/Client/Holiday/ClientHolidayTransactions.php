<?php

namespace App\Client\Holiday;

use Illuminate\Database\Eloquent\Model;

class ClientHolidayTransactions extends Model
{
    protected $guarded = ['id'];

    public function ClientHolidayDetails(){
        return $this->belongsTo('App\Client\Holiday\ClientHolidayDetails');
    }
}

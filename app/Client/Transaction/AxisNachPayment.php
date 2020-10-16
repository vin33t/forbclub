<?php

namespace App\Client\Transaction;

use Illuminate\Database\Eloquent\Model;

class AxisNachPayment extends Model
{
    protected $guarded = ['id'];

    public function Client(){
        return $this->belongsTo('App\Client\Client');
    }

    public function Meta(){
        return $this->belongsTo('App\Client\Transaction\AxisNachPaymentMeta','meta_id');
    }
}

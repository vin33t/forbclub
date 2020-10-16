<?php

namespace App\Client\Transaction;

use Illuminate\Database\Eloquent\Model;

class AxisNachPaymentMeta extends Model
{
    protected $guarded = ['id'];

    public function Payments(){
        return $this->hasMany('App\Client\Transaction\AxisNachPayment','meta_id');
    }
}

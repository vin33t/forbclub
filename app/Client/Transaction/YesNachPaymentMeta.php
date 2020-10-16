<?php

namespace App\Client\Transaction;

use Illuminate\Database\Eloquent\Model;

class YesNachPaymentMeta extends Model
{
    protected $guarded = ['id'];

    public function Payments(){
        return $this->hasMany('App\Client\Transaction\YesNachPayment','meta_id');
    }
}

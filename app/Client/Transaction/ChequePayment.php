<?php

namespace App\Client\Transaction;

use Illuminate\Database\Eloquent\Model;

class ChequePayment extends Model
{
  protected $guarded = ['id'];

  public function client()
  {
    return $this->belongsTo('App\Client\Client');
  }

  public function For()
  {
    return $this->hasMany('App\Client\Transaction\TransactionMonth')->where('transactionType','cheque');
  }
}

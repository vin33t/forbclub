<?php

namespace App\Client\Transaction;


use Illuminate\Database\Eloquent\Model;

class CashPayment extends Model
{
  protected $fillable = ['client_id', 'date_of_payment', 'amount', 'receipt_number', 'remarks', 'is_dp'];

  public function client()
  {
    return $this->belongsTo('App\Client\Client');
  }

  public function For()
  {
    return $this->hasMany('App\Client\Transaction\TransactionMonth')->where('transactionType','cash');
  }
}

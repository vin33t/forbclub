<?php

namespace App\Client;

use App\Client\Transaction\AxisNachPayment;
use App\Client\Transaction\YesNachPayment;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Client extends Model
{
  use HasSlug;

  use Searchable;

  public function getLatestPackageAttribute()
  {
    return $this->Packages->first();
  }

  public function getDownPaymentAttribute()
  {
    $cardPayments = $this->CardPayments->where('isDp',1)->pluck('amount')->sum();
    $cashPayments = $this->CashPayments->where('isDp',1)->pluck('amount')->sum();
    $chequePayments = $this->ChequePayments->where('isDp',1)->pluck('amount')->sum();
    $otherPayments = $this->OtherPayments->where('isDp',1)->pluck('amount')->sum();
    $payments = $cardPayments + $cashPayments + $chequePayments + $otherPayments;
    return $payments;
  }

  public function getPaidAmountAttribute()
  {
    $totalTransactions = collect();
    if($this->cashPayments->count()){
      foreach($this->CashPayments as $ca){
        $totalTransactions->push(['date'=>$ca->paymentDate,'amount'=>$ca->amount,'remarks'=>$ca->remarks,'mode'=>'Cash','dp'=>$ca->isDp]);
      }
    }
    if($this->cardPayments->count()){
      foreach($this->CardPayments as $cad){
        $totalTransactions->push(['date'=>$cad->paymentDate,'amount'=>$cad->amount,'remarks'=>$cad->remarks,'mode'=>'Card','dp'=>$cad->isDp]);
      }
    }
    if($this->chequePayments->count()){
      foreach($this->chequePayments as $che){
        $totalTransactions->push(['date'=>$che->paymentDate,'amount'=>$che->amount,'remarks'=>$che->remarks,'mode'=>'Cheque','dp'=>$che->isDp]);
      }
    }
    if($this->otherPayments->count()){
      foreach($this->otherPayments as $oth){
        $totalTransactions->push(['date'=>$oth->paymentDate,'amount'=>$oth->amount,'remarks'=>$oth->remarks,'mode'=>$oth->modeOfPayment,'dp'=>$oth->isDp]);
      }
    }

    if($this->AxisPayments->count()){
      foreach($this->AxisPayments as $axp){
        if($axp->status_description == 'success' or $axp->status_description == 'SUCCESS' or $axp->status_description == 'Success'){
          $totalTransactions->push(['date'=>$axp->date_of_transaction,'amount'=>$axp->amount,'remarks'=>$axp->reason_description,'mode'=>'AXIS NACH','dp'=>'']);
        }
      }
    }

    if($this->YesPayments->count()){
      foreach($this->YesPayments as $yep){
        if($yep->STATUS == 'ACCEPTED'){
          $totalTransactions->push(['date'=>$yep->VALUE_DATE,'amount'=>$yep->AMOUNT,'remarks'=>$yep->REASON_CODE,'mode'=>'YES NACH','dp'=>'']);
        }
      }
    }

    return $totalTransactions->pluck('amount')->sum();
  }

  public function getTransactionSummaryAttribute()
  {
    $cardPayments = $this->CardPayments;
    $cashPayments = $this->CashPayments;
    $chequePayments = $this->ChequePayments;
    $otherPayments = $this->OtherPayments;
    $axisNachPayments = $this->AxisPayments;
    $yesNachPayments = $this->YesPayments;
    $payments = [
      ' Card ('. $cardPayments->count() .') - <strong>' . inr($cardPayments->pluck('amount')->sum()) . '</strong>',
      ' Cash ('. $cashPayments->count() .') - <strong>' . inr($cashPayments->pluck('amount')->sum()) . '</strong>',
      ' Cheque ('. $chequePayments->count() .') - <strong>' . inr($chequePayments->pluck('amount')->sum()) . '</strong>',
      ' Others ('. $otherPayments->count() .') - <strong>' . inr($otherPayments->pluck('amount')->sum()) . '</strong>',
      ' Axis NACH ('. $axisNachPayments->count() .') - <strong>' . inr($axisNachPayments->pluck('amount')->sum()) . '</strong>',
      ' Yes NACH ('. $yesNachPayments->count() .') - <strong>' . inr($yesNachPayments->pluck('AMOUNT')->sum()) . '</strong>'
    ];
    return $payments;
  }


  public function getTransactionSummaryChartAttribute()
  {
    $cardPayments = $this->CardPayments;
    $cashPayments = $this->CashPayments;
    $chequePayments = $this->ChequePayments;
    $otherPayments = $this->OtherPayments;
    $axisNachPayments = $this->AxisPayments;
    $yesNachPayments = $this->YesPayments;



    $transactions = [];
    $totalPayments = [$cardPayments,$cashPayments,$chequePayments,$otherPayments,$axisNachPayments,$yesNachPayments];
    $modes = ['Card','Cash','Cheque','Others','Axis NACH','Yes NACH'];
    $count = 0;
    foreach ($totalPayments as $payment){
      if($payment->count()){
        if($modes[$count] == 'Yes NACH'){
          array_push($transactions, [
            'value' => $payment->pluck('AMOUNT')->sum(),
            'name' => $modes[$count],
          ]);
        }
        else{
          array_push($transactions, [
            'value' => $payment->pluck('amount')->sum(),
            'name' => $modes[$count],
          ]);
        }

      }
      $count++;
    }
    return collect($transactions);
  }
  public function getSlugOptions() : SlugOptions
  {
    return SlugOptions::create()
      ->generateSlugsFrom(['name','id'])
      ->saveSlugsTo('slug')
      ->doNotGenerateSlugsOnUpdate();
  }

  protected $guarded = ['id'];

  public function Packages()
  {
    return $this->hasMany('App\Client\Package\SoldPackages', 'clientId');
  }

  public function PackageBenefits()
  {
    return $this->hasMany('App\Client\Package\SoldPackageBenefits', 'clientId');
  }

  public function PackageExtension()
  {
    return $this->hasMany('App\Client\Package\SoldPackageExtension', 'clientId');
  }

  public function TimelineActivity()
  {
    return $this->hasMany('App\Client\TimelineActivity', 'client_id');
  }

  public function CardPayments()
  {
    return $this->hasMany('App\Client\Transaction\CardPayment','client_id');
  }

  public function CashPayments()
  {
    return $this->hasMany('App\Client\Transaction\CashPayment','client_id');
  }

  public function ChequePayments()
  {
    return $this->hasMany('App\Client\Transaction\ChequePayment','client_id');
  }

  public function OtherPayments()
  {
    return $this->hasMany('App\Client\Transaction\OtherPayment','client_id');
  }

  public function AxisPayments()
  {
    return $this->hasMany('App\Client\Transaction\AxisNachPayment','client_id');
  }

  public function YesPayments()
  {
    return $this->hasMany('App\Client\Transaction\YesNachPayment','client_id');
  }

  public function AxisMis()
  {
    return $this->hasMany('App\Client\Mis\AxisMis','client_id');
  }

  public function DisableNach(){
    return $this->hasMany('App\DisableNach','client_id');
  }

  public function Pdc(){
    return $this->hasMany('App\PDC','client_id');
  }

  public function Document(){
    return $this->hasOne('App\Document','client_id');
  }

  public function FollowUp(){
    return $this->hasMany('App\FollowUp','client_id');
  }

  public function Bookings(){
    return $this->hasMany('App\Client\Booking\Bookings','clientId');
  }

  public function User(){
    return $this->hasOne('App\User');
  }

  public function ClientHoliday(){
    return $this->hasMany('App\Client\Holiday\ClientHoliday');
  }

  public function RefundRequest(){
    return $this->hasOne('App\RefundRequests');
  }

  public function emails(){
    return $this->hasMany('App\Model\Emails\Emails');
  }

}

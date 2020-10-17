<?php

namespace App\Client;

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

}

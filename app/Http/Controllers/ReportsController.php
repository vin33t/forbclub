<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(){
      $clients = \App\Client\Client::all();

      $totalTransactions = collect();
      $totalDownPayment = 0;
      $totalEmis   = 0;
      $activeMemberAmount = 0;
      $cancelledMemberAmount = 0;
      foreach($clients as $client) {

        if ($client->cashPayments->count()) {
          foreach ($client->CashPayments as $ca) {
            $totalTransactions->push(['date' => $ca->paymentDate, 'amount' => $ca->amount, 'remarks' => $ca->remarks, 'mode' => 'Cash', 'dp' => $ca->isDp]);
            $ca->isDp ? $totalDownPayment += $ca->amount : $totalEmis += $totalDownPayment += $ca->amount;
            if($client->latestPackage->status == 'ACTIVE'){
                  $activeMemberAmount += $ca->amount;
            } elseif($client->latestPackage->status == 'CANCELLED'){
                  $cancelledMemberAmount += $ca->amount;
            }
          }
        }
        if ($client->cardPayments->count()) {
          foreach ($client->CardPayments as $cad) {
            $totalTransactions->push(['date' => $cad->paymentDate, 'amount' => $cad->amount, 'remarks' => $cad->remarks, 'mode' => 'Card', 'dp' => $cad->isDp]);
            $cad->isDp ? $totalDownPayment += $cad->amount : $totalEmis += $totalDownPayment += $cad->amount;
            if($client->latestPackage->status == 'ACTIVE'){
              $activeMemberAmount += $cad->amount;
            } elseif($client->latestPackage->status == 'CANCELLED'){
              $cancelledMemberAmount += $cad->amount;
            }
          }
        }
        if ($client->chequePayments->count()) {
          foreach ($client->chequePayments as $che) {
            $totalTransactions->push(['date' => $che->paymentDate, 'amount' => $che->amount, 'remarks' => $che->remarks, 'mode' => 'Cheque', 'dp' => $che->isDp]);
            $che->isDp ? $totalDownPayment += $che->amount : $totalEmis += $che->amount;
            if($client->latestPackage->status == 'ACTIVE'){
              $activeMemberAmount += $che->amount;
            } elseif($client->latestPackage->status == 'CANCELLED'){
              $cancelledMemberAmount += $che->amount;
            }
          }
        }
        if ($client->otherPayments->count()) {
          foreach ($client->otherPayments as $oth) {
            $totalTransactions->push(['date' => $oth->paymentDate, 'amount' => $oth->amount, 'remarks' => $oth->remarks, 'mode' => $oth->modeOfPayment, 'dp' => $oth->isDp]);
            $oth->isDp ? $totalDownPayment += $oth->amount : $totalEmis +=  $oth->amount;
            if($client->latestPackage->status == 'ACTIVE'){
              $activeMemberAmount += $oth->amount;
            } elseif($client->latestPackage->status == 'CANCELLED'){
              $cancelledMemberAmount += $oth->amount;
            }
          }
        }

        if ($client->AxisPayments->count()) {
          foreach ($client->AxisPayments as $axp) {
            if ($axp->status_description == 'success' or $axp->status_description == 'SUCCESS' or $axp->status_description == 'Success') {
              $totalTransactions->push(['date' => $axp->date_of_transaction, 'amount' => $axp->amount, 'remarks' => $axp->reason_description, 'mode' => 'AXIS NACH', 'dp' => 0]);
              $totalEmis +=  $axp->amount;
              if($client->latestPackage->status == 'ACTIVE'){
                $activeMemberAmount += $axp->amount;
              } elseif($client->latestPackage->status == 'CANCELLED'){
                $cancelledMemberAmount += $axp->amount;
              }
            }
          }
        }

        if ($client->YesPayments->count()) {
          foreach ($client->YesPayments as $yep) {
            if ($yep->STATUS == 'ACCEPTED') {
              $totalTransactions->push(['date' => $yep->VALUE_DATE, 'amount' => $yep->AMOUNT, 'remarks' => $yep->REASON_CODE, 'mode' => 'YES NACH', 'dp' => 0]);
              $totalEmis +=  $yep->amount;
              if($client->latestPackage->status == 'ACTIVE'){
                $activeMemberAmount += $yep->amount;
              } elseif($client->latestPackage->status == 'CANCELLED'){
                $cancelledMemberAmount += $yep->amount;
              }

            }
          }
        }
      }

      $totalPaymentReceived = $totalTransactions->pluck('amount')->sum();
      $firstPayment = $totalTransactions->first()['date'];
      $latestPayment = $totalTransactions->last()['date'];
      $status = ['ACTIVE', 'CANCELLED'];
      $clientStatus = null;
      foreach($status as $clientSt){
        $clientStatus[$clientSt] = \App\Client\Package\SoldPackages::where('status',$clientSt)->count();
      }
//      return $totalPaymentReceived;
      return view('reports.home')->with([
        'clients' => $clients,
        'totalPaymentReceived'=> $totalPaymentReceived,
        'totalDownPayment'=> $totalDownPayment,
        'totalEmis'=> $totalEmis,
        'firstPayment'=> $firstPayment,
        'latestPayment'=> $latestPayment,
        'clientStatus'=> $clientStatus,
        'activeMemberAmount'=> $activeMemberAmount,
        'cancelledMemberAmount'=> $cancelledMemberAmount,
      ]);
    }
}

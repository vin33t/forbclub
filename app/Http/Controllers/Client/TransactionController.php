<?php

namespace App\Http\Controllers\Client;

use App\Client\Client;
use App\Client\Mis\AxisMis;
use App\Client\TimelineActivity;
use App\Client\Transaction\AxisNachPayment;
use App\Client\Transaction\CardPayment;
use App\Client\Transaction\CashPayment;
use App\Client\Transaction\TransactionMonth;
use App\Client\Transaction\YesNachPayment;
use App\DisableNach;
use App\Http\Controllers\Controller;
use App\PDC;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Rap2hpoutre\FastExcel\FastExcel;



class TransactionController extends Controller
{
  public function createCard(Request $request, $clientId)
  {
    $request->validate([
      'paymentReceivedOn'=>'required|date',
      'paymentAmount'=>'required|integer',
      'paymentCardType'=>'required|string',
      'paymentCardProvider'=>'required|string',
      'paymentCardLastFourDigits'=>'required|integer',
      'paymentCardRemarks'=>'required|string',
    ]);
    if($request->has('paymentDownPayment') AND $request->has('paymentAddon')){
      notifyToast('error','OOPS!!', 'A payment can either be Addon or Down payment.');
    }
    else {
      DB::beginTransaction();
      try {
        $transaction = new CardPayment;
        $transaction->client_id = $clientId;
        $transaction->paymentDate = $request->paymentReceivedOn;
        $transaction->amount = $request->paymentAmount;
        $transaction->cardType = $request->paymentCardType;
        $transaction->bankName = $request->paymentCardProvider;
        $transaction->cardLastFourDigits = $request->paymentCardLastFourDigits;
        $transaction->isDp = $request->paymentDownPayment ? 1 : 0;
        $transaction->isAddon = $request->paymentAddOn ? 1 : 0;
        $transaction->remarks = $request->paymentCardRemarks;
        $transaction->save();
        if($request->has('paymentForMonth')){
          $co = 0;
          foreach ($request->paymentForMonth as $month){
            $transactionMonth = new TransactionMonth;
////            return $request->paymentForMonth;
//            $transactionMonth->transaction_id  =$transaction->id;
//            $transactionMonth->paidMonth  =$month;
//            $transactionMonth->paidYear  = $request->paymentForYear[$co];
//            $transactionMonth->transactionType  = 'card';
//            $transactionMonth->save();
            $transactionMonth->create([
              'transaction_id'=>$transaction->id,
              'paidMonth'=>$month,
              'paidYear'=>$request->paymentForYear[$co],
              'transactionType'=>'card',
            ]);
            $co ++;
          }
        }
        (new TimelineActivity)->create([
          'user_id' => Auth::user()->id,
          'client_id' => $clientId,
          'title' => 'Card Payment',
          'parent_model' => 'App\Client\Transaction\CardPayment',
          'parent_id' => $transaction->id,
          'body' => 'Client made a payment of ' . inr($transaction->amount) . ' using Card (' . $transaction->cardType . ') ending wth ' . ' card ending with ' . $transaction->cardLastFourDigits . ' for the month of ' . Carbon::parse($request->paymentReceivedOn)->format('F Y')
        ]);
        DB::commit();
        notifyToast('success', 'Saved', 'Card Transaction Saved');
      } catch (\Exception $e) {
        DB::rollBack();
      }
    }
    return redirect()->back();

  }

  public function createCash(Request $request, $clientId)
  {
    $request->validate([
      'paymentReceivedOn'=>'required|date',
      'paymentAmount'=>'required|integer',
      'paymentReceiptNumber'=>'required|string',
      'paymentCashRemarks'=>'required|string',
    ]);
    if($request->has('paymentDownPayment') AND $request->has('paymentAddon')){
      notifyToast('error','OOPS!!', 'A payment can either be Addon or Down payment.');
    }
    else {
      DB::beginTransaction();
      try {
        $transaction = new CashPayment;
        $transaction->client_id = $clientId;
        $transaction->paymentDate = $request->paymentReceivedOn;
        $transaction->amount = $request->paymentAmount;
        $transaction->receiptNumber = $request->paymentReceiptNumber;
        $transaction->isDp = $request->paymentDownPayment ? 1 : 0;
        $transaction->isAddon = $request->paymentAddOn ? 1 : 0;
        $transaction->remarks = $request->paymentCashRemarks;
        $transaction->save();
        if($request->has('paymentForMonth')){
          $co = 0;
          foreach ($request->paymentForMonth as $month){
            $transactionMonth = new TransactionMonth;
            $transactionMonth->create([
              'transaction_id'=>$transaction->id,
              'paidMonth'=>$month,
              'paidYear'=>$request->paymentForYear[$co],
              'transactionType'=>'cash',
            ]);
            $co ++;
          }
        }
        (new TimelineActivity)->create([
          'user_id' => Auth::user()->id,
          'client_id' => $clientId,
          'title' => 'Cash Payment',
          'parent_model' => 'App\Client\Transaction\CashPayment',
          'parent_id' => $transaction->id,
          'body' => 'Client made a payment of ' . inr($transaction->amount) . ' paid in Cash (' . $transaction->receiptNumber . ') for the month of ' . Carbon::parse($request->paymentReceivedOn)->format('F Y')
        ]);
        DB::commit();
        notifyToast('success', 'Saved', 'Cash Transaction Saved');
      } catch (\Exception $e) {
//        dd($e);
        DB::rollBack();
      }
    }
    return redirect()->back();
  }

  public function importHistory(){
    return view('client.transaction.importHistory');
  }

  public function importHistoryDetails($importId, $bank){
    if($bank == 'yes'){
      $transactions = YesNachPayment::where('meta_id',$importId)->get();
    }elseif($bank == 'axis'){
      $transactions = AxisNachPayment::where('meta_id',$importId)->get();
    }
    return view('client.transaction.importDetails')->with('transactions',$transactions)->with('bank',$bank);
  }

  public function downloadAxisMis(){
    return view('client.downloadAxisMis');
  }

  public function downloadAxisMisFile(Request $request){
    $collection = collect();
    $clients = Client::find(AxisMis::where('client_id','!=',null)->pluck('client_id')->unique());
    foreach($clients as $client){
      $foo = collect();
      $foo->put('CLIENT NAME',$client->name);
      $foo->put('UMRNNO',$client->AxisMis->sortBy('created_at')->last()->UMRNNO);
      $foo->put('SYSTEM_STATUS',$client->AxisMis->sortBy('created_at')->last()->SYSTEM_STATUS);
      $foo->put('REASONNAME',$client->AxisMis->sortBy('created_at')->last()->REASONNAME);
      $foo->put('DEBTOR_CUSTOMER_REFERENCE_NO',$client->AxisMis->sortBy('created_at')->last()->DEBTOR_CUSTOMER_REFERENCE_NO);
      $foo->put('PAYMENTTYPE',$client->AxisMis->sortBy('created_at')->last()->PAYMENTTYPE);
      $foo->put('DEBTORACCOUNTNO',$client->AxisMis->sortBy('created_at')->last()->DEBTORACCOUNTNO);
      $foo->put('DEBITORBANKNAME',$client->AxisMis->sortBy('created_at')->last()->DEBITORBANKNAME);
      $foo->put('DEBTORBANKCODE',$client->AxisMis->sortBy('created_at')->last()->DEBTORBANKCODE);
      $foo->put('DEBTORNAME',$client->AxisMis->sortBy('created_at')->last()->DEBTORNAME);
      $foo->put('CREDITORNAME',$client->AxisMis->sortBy('created_at')->last()->CREDITORNAME);
      $foo->put('FREQUENCY',$client->AxisMis->sortBy('created_at')->last()->FREQUENCY);
      $foo->put('AMOUNT',$client->AxisMis->sortBy('created_at')->last()->AMOUNT);
      $foo->put('STARTDATE',$client->AxisMis->sortBy('created_at')->last()->STARTDATE);
      $foo->put('ENDDATE',$client->AxisMis->sortBy('created_at')->last()->ENDDATE);
      $foo->put('MANDATE_INITIATED_BUSINESS_DATE',$client->AxisMis->sortBy('created_at')->last()->MANDATE_INITIATED_BUSINESS_DATE);
      $foo->put('SPONSOR_CHECKER_APPROVAL_DATE',$client->AxisMis->sortBy('created_at')->last()->SPONSOR_CHECKER_APPROVAL_DATE);
      $foo->put('MANDATE_CREATION_DATE',$client->AxisMis->sortBy('created_at')->last()->MANDATE_CREATION_DATE);
      $foo->put('MANDATE_ACCEPTANCE_DATE',$client->AxisMis->sortBy('created_at')->last()->MANDATE_ACCEPTANCE_DATE);
      $foo->put('CREDITORUTILITYCODE',$client->AxisMis->sortBy('created_at')->last()->CREDITORUTILITYCODE);
      $foo->put('PRO_DATE',$client->AxisMis->sortBy('created_at')->last()->PRO_DATE);
      $foo->put('LOT',$client->AxisMis->sortBy('created_at')->last()->LOT);
      $foo->put('SRNO',$client->AxisMis->sortBy('created_at')->last()->SRNO);
      $foo->put('CLIENT_COD',$client->AxisMis->sortBy('created_at')->last()->CLIENT_COD);
      $foo->put('OLD_UMRN',$client->AxisMis->sortBy('created_at')->last()->OLD_UMRN);
      $foo->put('DATE',$client->AxisMis->sortBy('created_at')->last()->DATE);
      $foo->put('SP_BKCODE',$client->AxisMis->sortBy('created_at')->last()->SP_BKCODE);
      $foo->put('ACTION',$client->AxisMis->sortBy('created_at')->last()->ACTION);
      $foo->put('AC_TYPE',$client->AxisMis->sortBy('created_at')->last()->AC_TYPE);
      $foo->put('MOBILE',$client->AxisMis->sortBy('created_at')->last()->MOBILE);
      $foo->put('PICKUP_LOC',$client->AxisMis->sortBy('created_at')->last()->PICKUP_LOC);
      $foo->put('INWARD_DATE',$client->AxisMis->sortBy('created_at')->last()->INWARD_DATE);
      $foo->put('SP_BANK',$client->AxisMis->sortBy('created_at')->last()->SP_BANK);
      $foo->put('SCHEME',$client->AxisMis->sortBy('created_at')->last()->SCHEME);
      if(Carbon::parse($client->AxisMis->sortBy('created_at')->last()->ENDDATE)->gt(Carbon::now())){
        $collection->push($foo);
      }
    }
    $fileName = $request->month .' - ' .$request->year.'-AxisMis'.time().rand() .'.xlsx';
    (new FastExcel($collection))->export('excel/'.$fileName);
    $link = url('/excel/'.$fileName);
    return Redirect::to($link);
    // session(['axis-mis-collection' => $collection]);

    // return Excel::download(new AxisMisExport, $request->month .' - ' .$request->year.'-AxisMis.xlsx');
  }


  public function uploadAxisMis(){
    return view('client.uploadAxisMis');
  }
  public function uploadAxisMisFile(Request $request){
    return \redirect()->back();
  }

  public function uploadTransaction(){
    return view('client.transaction.upload');
  }

  public function uploadTransactionFile(Request $request){
    return \redirect()->back();
  }

  public function disableNach(Request $request){
//    return $request;
    try {
      DisableNach::create([
        'client_id' => $request->client,
        'month' => Carbon::parse($request->month)->format('m'),
        'year' => Carbon::parse($request->month)->format('Y'),
        'remarks' => $request->remarks,
        'permanent' => $request->permanent ? 1 : 0,
        'bank' => Client::find($request->client)->AxisPayments->count() ? 'AXIS' : 'YES'
      ]);
      return \redirect()->back();
    }
    catch( \Exception $e){
      return \redirect()->back()->withErrors($e);
    }
  }


  public function addPdc(Request $request){
    $v = Validator::make($request->all(),[
      'cheque_number' => 'required',
      'date_of_execution' => 'required',
      'amount' => 'required',
    ]);

    if ($v->fails()) {
      notification('Opps!!','Please Fix the errors', 'warning','okay');
      return redirect()->back()->withErrors($v)->withInput();
    }
    $pdc = new PDC;
    $pdc->client_id = $request->client;
    $pdc->cheque_no = $request->cheque_number;
    $pdc->date_of_execution = $request->date_of_execution;
    $pdc->amount = $request->amount;
    $pdc->micr_number = $request->micr_number;
    $pdc->branch_name = $request->branch_name;
    $pdc->branch_address = $request->branch_address;
    $pdc->remarks= $request->remarks;
    $pdc->employee_id = Auth::user()->id;
    $pdc->save();

    return redirect()->back()->withSuccess('PDC Added');
  }
}


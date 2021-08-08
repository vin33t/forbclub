<?php

namespace App\Http\Controllers\Client;

use App\AsfPayments;
use App\Client\Client;
use App\Client\Mis\AxisMis;
use App\Client\Package\SoldPackages;
use App\Client\TimelineActivity;
use App\Client\Transaction\AxisNachPayment;
use App\Client\Transaction\AxisNachPaymentMeta;
use App\Client\Transaction\CardPayment;
use App\Client\Transaction\CashPayment;
use App\Client\Transaction\ChequePayment;
use App\Client\Transaction\OtherPayment;
use App\Client\Transaction\TransactionMonth;
use App\Client\Transaction\YesNachPayment;
use App\Client\Transaction\YesNachPaymentMeta;
use App\DisableNach;
use App\Http\Controllers\Controller;
use App\PDC;
use App\Reimbursement;
use App\Venue;
use App\VenueExpenses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Rap2hpoutre\FastExcel\FastExcel;


class TransactionController extends Controller
{
  public function createCheque(Request $request, $clientId)
  {
//    return $request;
    $request->validate([
      'chequeClearedOn' => 'required|date',
      'paymentAmount' => 'required|integer',
      'paymentChequeNumber' => 'required|string',
      'paymentChequeRemarks' => 'required|string',
      'paymentChequeIssuer' => 'required|string',
      'paymentChequeClearingBank' => 'required|string',
    ]);
    if ($request->has('paymentDownPayment') and $request->has('paymentAddon')) {
      notifyToast('error', 'OOPS!!', 'A payment can either be Addon or Down payment.');
    } else {
      DB::beginTransaction();
      try {
        $transaction = new ChequePayment();
        $transaction->client_id = $clientId;
        $transaction->paymentDate = $request->chequeClearedOn;
        $transaction->amount = $request->paymentAmount;
        $transaction->chequeNumber = $request->paymentChequeNumber;
        $transaction->isDp = $request->paymentDownPayment ? 1 : 0;
        $transaction->isAddon = $request->paymentAddOn ? 1 : 0;
        $transaction->remarks = $request->paymentChequeRemarks;
        $transaction->chequeIssuer = $request->paymentChequeIssuer;
        $transaction->chequeClearingBank = $request->paymentChequeClearingBank;
        $transaction->save();
        if ($request->has('paymentForMonth')) {
          $co = 0;
          foreach ($request->paymentForMonth as $month) {
            $transactionMonth = new TransactionMonth;
////            return $request->paymentForMonth;
//            $transactionMonth->transaction_id  =$transaction->id;
//            $transactionMonth->paidMonth  =$month;
//            $transactionMonth->paidYear  = $request->paymentForYear[$co];
//            $transactionMonth->transactionType  = 'card';
//            $transactionMonth->save();
            $transactionMonth->create([
              'transaction_id' => $transaction->id,
              'paidMonth' => $month,
              'paidYear' => $request->paymentForYear[$co],
              'transactionType' => 'cheque',
            ]);
            $co++;
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
        notifyToast('success', 'Saved', 'Cheque Transaction Saved');
      } catch (\Exception $e) {
        DB::rollBack();
      }
    }
    return redirect()->back();
  }

  public function createCard(Request $request, $clientId)
  {
    $request->validate([
      'paymentReceivedOn' => 'required|date',
      'paymentAmount' => 'required|integer',
      'paymentCardType' => 'required|string',
      'paymentCardProvider' => 'required|string',
      'paymentCardLastFourDigits' => 'required',
      'paymentCardRemarks' => 'required|string',
    ]);
    if ($request->has('paymentDownPayment') and $request->has('paymentAddon')) {
      notifyToast('error', 'OOPS!!', 'A payment can either be Addon or Down payment.');
    } else {
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
        if ($request->has('paymentForMonth')) {
          $co = 0;
          foreach ($request->paymentForMonth as $month) {
            $transactionMonth = new TransactionMonth;
////            return $request->paymentForMonth;
//            $transactionMonth->transaction_id  =$transaction->id;
//            $transactionMonth->paidMonth  =$month;
//            $transactionMonth->paidYear  = $request->paymentForYear[$co];
//            $transactionMonth->transactionType  = 'card';
//            $transactionMonth->save();
            $transactionMonth->create([
              'transaction_id' => $transaction->id,
              'paidMonth' => $month,
              'paidYear' => $request->paymentForYear[$co],
              'transactionType' => 'card',
            ]);
            $co++;
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
      'paymentReceivedOn' => 'required|date',
      'paymentAmount' => 'required|integer',
      'paymentReceiptNumber' => 'required|string',
      'paymentCashRemarks' => 'required|string',
    ]);
    if ($request->has('paymentDownPayment') and $request->has('paymentAddon')) {
      notifyToast('error', 'OOPS!!', 'A payment can either be Addon or Down payment.');
    } else {
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
        if ($request->has('paymentForMonth')) {
          $co = 0;
          foreach ($request->paymentForMonth as $month) {
            $transactionMonth = new TransactionMonth;
            $transactionMonth->create([
              'transaction_id' => $transaction->id,
              'paidMonth' => $month,
              'paidYear' => $request->paymentForYear[$co],
              'transactionType' => 'cash',
            ]);
            $co++;
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


  public function importHistory()
  {
    return view('client.transaction.importHistory');
  }

  public function importHistoryDetails($importId, $bank)
  {
    if ($bank == 'yes') {
      $transactions = YesNachPayment::where('meta_id', $importId)->get();
    } elseif ($bank == 'axis') {
      $transactions = AxisNachPayment::where('meta_id', $importId)->get();
    }
    return view('client.transaction.importDetails')->with('transactions', $transactions)->with('bank', $bank);
  }

  public function downloadAxisMis()
  {
    return view('client.downloadAxisMis');
  }

  public function downloadAxisMisFile(Request $request)
  {
//    return $request;
    $collection = collect();
    $clients = Client::find(AxisMis::where('client_id', '!=', null)->pluck('client_id')->unique());
    foreach ($clients as $client) {
      $foo = collect();
      $foo->put('CLIENT NAME', $client->name);
      $foo->put('UMRNNO', $client->AxisMis->sortBy('created_at')->last()->UMRNNO);
      $foo->put('SYSTEM_STATUS', $client->AxisMis->sortBy('created_at')->last()->SYSTEM_STATUS);
      $foo->put('REASONNAME', $client->AxisMis->sortBy('created_at')->last()->REASONNAME);
      $foo->put('DEBTOR_CUSTOMER_REFERENCE_NO', $client->AxisMis->sortBy('created_at')->last()->DEBTOR_CUSTOMER_REFERENCE_NO);
      $foo->put('PAYMENTTYPE', $client->AxisMis->sortBy('created_at')->last()->PAYMENTTYPE);
      $foo->put('DEBTORACCOUNTNO', $client->AxisMis->sortBy('created_at')->last()->DEBTORACCOUNTNO);
      $foo->put('DEBITORBANKNAME', $client->AxisMis->sortBy('created_at')->last()->DEBITORBANKNAME);
      $foo->put('DEBTORBANKCODE', $client->AxisMis->sortBy('created_at')->last()->DEBTORBANKCODE);
      $foo->put('DEBTORNAME', $client->AxisMis->sortBy('created_at')->last()->DEBTORNAME);
      $foo->put('CREDITORNAME', $client->AxisMis->sortBy('created_at')->last()->CREDITORNAME);
      $foo->put('FREQUENCY', $client->AxisMis->sortBy('created_at')->last()->FREQUENCY);
      $foo->put('AMOUNT', $client->AxisMis->sortBy('created_at')->last()->AMOUNT);
      $foo->put('STARTDATE', $client->AxisMis->sortBy('created_at')->last()->STARTDATE);
      $foo->put('ENDDATE', $client->AxisMis->sortBy('created_at')->last()->ENDDATE);
      $foo->put('MANDATE_INITIATED_BUSINESS_DATE', $client->AxisMis->sortBy('created_at')->last()->MANDATE_INITIATED_BUSINESS_DATE);
      $foo->put('SPONSOR_CHECKER_APPROVAL_DATE', $client->AxisMis->sortBy('created_at')->last()->SPONSOR_CHECKER_APPROVAL_DATE);
      $foo->put('MANDATE_CREATION_DATE', $client->AxisMis->sortBy('created_at')->last()->MANDATE_CREATION_DATE);
      $foo->put('MANDATE_ACCEPTANCE_DATE', $client->AxisMis->sortBy('created_at')->last()->MANDATE_ACCEPTANCE_DATE);
      $foo->put('CREDITORUTILITYCODE', $client->AxisMis->sortBy('created_at')->last()->CREDITORUTILITYCODE);
      $foo->put('PRO_DATE', $client->AxisMis->sortBy('created_at')->last()->PRO_DATE);
      $foo->put('LOT', $client->AxisMis->sortBy('created_at')->last()->LOT);
      $foo->put('SRNO', $client->AxisMis->sortBy('created_at')->last()->SRNO);
      $foo->put('CLIENT_COD', $client->AxisMis->sortBy('created_at')->last()->CLIENT_COD);
      $foo->put('OLD_UMRN', $client->AxisMis->sortBy('created_at')->last()->OLD_UMRN);
      $foo->put('DATE', $client->AxisMis->sortBy('created_at')->last()->DATE);
      $foo->put('SP_BKCODE', $client->AxisMis->sortBy('created_at')->last()->SP_BKCODE);
      $foo->put('ACTION', $client->AxisMis->sortBy('created_at')->last()->ACTION);
      $foo->put('AC_TYPE', $client->AxisMis->sortBy('created_at')->last()->AC_TYPE);
      $foo->put('MOBILE', $client->AxisMis->sortBy('created_at')->last()->MOBILE);
      $foo->put('PICKUP_LOC', $client->AxisMis->sortBy('created_at')->last()->PICKUP_LOC);
      $foo->put('INWARD_DATE', $client->AxisMis->sortBy('created_at')->last()->INWARD_DATE);
      $foo->put('SP_BANK', $client->AxisMis->sortBy('created_at')->last()->SP_BANK);
      $foo->put('SCHEME', $client->AxisMis->sortBy('created_at')->last()->SCHEME);
      if($client->DisableNach->count()){
        if($client->DisableNach()->where('permanent',1)->get()->count()){
          $foo->put('DEBIT','NO');
        }
        else{
          if($client->DisableNach()->where('year',$request->year)->where('month',$request->month)->first()){
            $foo->put('DEBIT','NO');
          }else{
            $foo->put('DEBIT','YES');
          }
        }
      } else {
            $foo->put('DEBIT','YES');
      }

      if (Carbon::parse($client->AxisMis->sortBy('created_at')->last()->ENDDATE)->gt(Carbon::now())) {
        $collection->push($foo);
      }
    }
    $fileName = $request->month . ' - ' . $request->year . '-AxisMis' . time() . rand() . '.xlsx';
    (new FastExcel($collection))->export('excel/' . $fileName);
    $link = url('/excel/' . $fileName);
    return Redirect::to($link);
    // session(['axis-mis-collection' => $collection]);

    // return Excel::download(new AxisMisExport, $request->month .' - ' .$request->year.'-AxisMis.xlsx');
  }


  public function uploadAxisMis()
  {
    return view('client.uploadAxisMis');
  }

  public function uploadAxisMisFile(Request $request)
  {
    return \redirect()->back();
  }

  public function uploadTransaction()
  {
    return view('client.transaction.upload');
  }

  public function uploadTransactionFile(Request $request)
  {

    $data = (new FastExcel)->import($request->transactionFile);

    if ($request->bank == 'Yes') {
      try {
        if ($data[0]['VALUE DATE'] == "") {
          return \redirect()->back()->withErrors(['error' => 'VALUE DATE column cannot be empty. This Column contains the date of the transactions']);
        }

        $success = 0;
        $transactions_count = 0;
        $success_amount = 0;
        $failure = 0;
        $failure_amount = 0;
        $meta = new YesNachPaymentMeta();
        $meta->file_name = $request->transactionFile->getClientOriginalName();
        $meta->amount = $data->pluck('AMOUNT')->sum();
        $meta->success = 0;
        $meta->transactions = $transactions_count;
        $meta->success_amount = 0;
        $meta->failure = 0;
        $meta->failure_amount = 0;
        $meta->upload_date = Carbon::parse(Carbon::createFromFormat('Y-d-m', Carbon::parse($data[0]['VALUE DATE'])->format('Y-m-d')))->format('Y-m-d');
        $meta->save();
        foreach ($data as $tran) {
          $oldPay = YesNachPayment::where('RECEIVER_ACCOUNT', $tran['RECEIVER ACCOUNT'])->get();
          if ($oldPay->count() > 0) {
            $id = $oldPay->first()->client_id;
          } else {
            $id = null;
          }
          $transaction = new YesNachPayment();
          $transaction->meta_id = $meta->id;
          $transaction->client_id = $id;
          $transaction->ITEM_TYPE = $tran['ITEM TYPE'];
          $transaction->ITEM_REFERENCE = $tran['ITEM REFERENCE'];
          $transaction->ITEM_SEQUENCE_NUMBER = $tran['ITEM SEQUENCE NUMBER'];
          $transaction->STATUS = $tran['STATUS'];
          $transaction->CLEARING_STATUS = $tran['CLEARING STATUS'];
          $transaction->VALUE_DATE = Carbon::parse(Carbon::createFromFormat('Y-d-m', Carbon::parse($tran['VALUE DATE'])->format('Y-m-d')))->format('Y-m-d');
          $transaction->SENDER = $tran['SENDER'];
          $transaction->RECEIVER = $tran['RECEIVER'];
          $transaction->REASON_CODE = $tran['REASON CODE'];
          $transaction->CURRENCY = $tran['CURRENCY'];
          $transaction->AMOUNT = $tran['AMOUNT'];
          $transaction->RECEIVER_ACCOUNT = $tran['RECEIVER ACCOUNT'];
          $transaction->NAME = $tran['NAME'];
          $transaction->save();
          if ($tran['STATUS'] == 'RETURNED') {
            $success += 1;
            $success_amount += $tran['AMOUNT'];
          } elseif ($tran['STATUS'] == 'ACCEPTED') {
            $failure += 1;
            $failure_amount += $tran['AMOUNT'];
          }
          $transactions_count += 1;
        }
        $meta->success = $success;
        $meta->success_amount = $success_amount;
        $meta->failure = $failure;
        $meta->failure_amount = $failure_amount;
        $meta->transactions = $transactions_count;
        $meta->save();
      } catch (\Exception $e) {
        return \redirect()->back()->withErrors($e->getMessage());
      }

    } else {

      try {

        $success = 0;
        $transactions_count = 0;
        $success_amount = 0;
        $failure = 0;
        $failure_amount = 0;
        $meta = new AxisNachPaymentMeta;
        $meta->file_name = $request->transactionFile->getClientOriginalName();
        $meta->amount = $data->pluck('Amount (Rs)')->sum();
        $meta->success = 0;
        $meta->transactions = $transactions_count;
        $meta->success_amount = 0;
        $meta->failure = 0;
        $meta->failure_amount = 0;
        $meta->upload_date = Carbon::parse(Carbon::createFromFormat('d/m/Y', $data[0]['Date of Txn']))->format('Y-m-d');
        $meta->save();
        foreach ($data as $tran) {

          $prefix = array("F", "T", "K", "-", "f", "t", "k", "l", "p", "L", "P", "c", "C", "@", " ");
//        dd($row);
          $id = $tran['Transaction ID/REF'];
          $onlyId = str_replace($prefix, "", $id);
          if ($onlyId == 552286) {
            $package = SoldPackages::where('fclpId', '552226')->get();
//        $package = Client::query()->where('application_no','552226')->get();
          } else {
            $package = SoldPackages::where('fclpId', $onlyId)->get();
//        $package = Client::query()->where('application_no', 'like', '%'.$onlyId.'%')->get();
          }
//      return $tran;
          $transaction = new AxisNachPayment;
          $transaction->meta_id = $meta->id;
          if ($package) {
            $transaction->client_id = $package->first()->clientId;
          }
          $transaction->corporate_user_no = $tran['Corporate User No'];
          $transaction->corporate_name = $tran['Corporate Name'];
          $transaction->umrn = $tran['UMRN'];
          $transaction->customer_to_be_debited = $tran['Customer to be debited'];
          $transaction->customer_ifsc = $tran['Customer IFSC'];
          $transaction->customer_debit_ac = $tran['Customer Debit AC'];
          $transaction->transaction_id_ref = $tran['Transaction ID/REF'];
          $transaction->amount = $tran['Amount (Rs)'];
          $transaction->date_of_transaction = Carbon::parse(Carbon::createFromFormat('d/m/Y', $data[0]['Date of Txn']))->format('Y-m-d');
          $transaction->status_description = $tran['Status'];
          $transaction->reason_description = $tran['Reason Description'];
          $transaction->save();
          if ($tran['Status'] == 'SUCCESS') {
            $success += 1;
            $success_amount += $tran['Amount (Rs)'];
          } elseif ($tran['Status'] == 'FAILURE') {
            $failure += 1;
            $failure_amount += $tran['Amount (Rs)'];
          }
          $transactions_count += 1;
        }
        $meta->success = $success;
        $meta->success_amount = $success_amount;
        $meta->failure = $failure;
        $meta->failure_amount = $failure_amount;
        $meta->transactions = $transactions_count;
        $meta->save();
      } catch (\Exception $e) {
        return \redirect()->back()->withErrors($e->getMessage());
      }
    }

//    return $request;
    return redirect()->back()->with('message', 'FILE UPLOADED');
  }

  public function disableNach(Request $request)
  {
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
    } catch (\Exception $e) {
      return \redirect()->back()->withErrors($e);
    }
  }


  public function addPdc(Request $request)
  {
    $v = Validator::make($request->all(), [
      'cheque_number' => 'required',
      'date_of_execution' => 'required',
      'amount' => 'required',
    ]);

    if ($v->fails()) {
      notification('Opps!!', 'Please Fix the errors', 'warning', 'okay');
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
    $pdc->remarks = $request->remarks;
    $pdc->status = 'unused';
    $pdc->employee_id = Auth::user()->id;
    $pdc->save();

    return redirect()->back()->withSuccess('PDC Added');
  }

  public function updatePdc(Request $request)
  {
    $v = Validator::make($request->all(), [
      'cheque_number' => 'required',
      'date_of_execution' => 'required',
      'amount' => 'required',
      'status' => 'required',
    ]);

    if ($v->fails()) {
      return redirect()->back()->withErrors($v)->withInput();
    }
    $pdc = PDC::find($request->pdc);
    $pdc->cheque_no = $request->cheque_number;
    $pdc->date_of_execution = $request->date_of_execution;
    $pdc->amount = $request->amount;
    $pdc->micr_number = $request->micr_number;
    $pdc->remarks = $request->remarks;
    $pdc->status = $request->status;
    if ($request->status == 'CLEARED') {
      $transaction = new ChequePayment();
      $transaction->client_id = $pdc->client_id;
      $transaction->paymentDate = $pdc->date_of_execution;
      $transaction->amount = $pdc->amount;
      $transaction->chequeNumber = $pdc->cheque_no;
      $transaction->isDp = 0;
      $transaction->isAddon = 0;
      $transaction->remarks = $pdc->remarks;
      $transaction->chequeIssuer = $pdc->branch_name;
      $transaction->chequeClearingBank = '';
      $transaction->save();
      $pdc->transaction_id = $transaction->id;
    }
    $pdc->save();

    return redirect()->back()->withSuccess('PDC Added');
  }

  public function updatePdcStatus(Request $request, $id)
  {
    $pdc = PDC::find($id);
    if ($pdc) {
      if ($request->status == 'CLEARED') {
        $transaction = new ChequePayment();
        $transaction->client_id = $pdc->client_id;
        $transaction->paymentDate = $pdc->date_of_execution;
        $transaction->amount = $pdc->amount;
        $transaction->chequeNumber = $pdc->cheque_no;
        $transaction->isDp = 0;
        $transaction->isAddon = 0;
        $transaction->remarks = $pdc->remarks;
        $transaction->chequeIssuer = $pdc->branch_name;
        $transaction->chequeClearingBank = '';
        $transaction->save();
        $pdc->transaction_id = $transaction->id;
      }
      $pdc->status = $request->status;
      $pdc->save();

      return \redirect()->back();
    }
  }

  public function reimbursementIndex()
  {
    if (\request()->month | \request()->year) {
      $year = \request()->year;
      $month = \request()->month;
      $start = Carbon::createFromDate($year, $month)->startOfMonth()->addDays(1);
      $end = Carbon::createFromDate($year, $month)->endOfMonth();
      $reimbursements = Reimbursement::whereBetween('expenseDate', [$start, $end])->get();
    } else {
      $reimbursements = \App\Reimbursement::all()->sortByDesc('expenseDate');
    }
    return view('client.transaction.Reimbursement.index')->with('reimbursements', $reimbursements);
  }

  public function reimbursementSummary()
  {
    if (Reimbursement::all()->count()) {
      $start = \Carbon\Carbon::parse(Reimbursement::all()->sortByDesc('expenseDate')->first()->expenseDate)->startOfYear();
      $end = \Carbon\Carbon::parse(Venue::all()->sortByDesc('expenseDate')->last()->expenseDate)->endOfYear();


      $interval = \DateInterval::createFromDateString('1 month');
      $period = new \DatePeriod($start, $interval, $end);
      $dates = collect();
      foreach ($period as $dt) {
        $dates->push($dt->format("Y-m"));
      }
      $summary = collect();
      foreach ($dates as $date) {
        $year = explode('-', $date)[0];
        $month = explode('-', $date)[1];
        $start = Carbon::createFromDate($year, $month)->startOfMonth()->addDays(1);
        $end = Carbon::createFromDate($year, $month)->endOfMonth();
        $reimbursements = Reimbursement::whereBetween('expenseDate', [$start, $end])->get();

        $reimbursementClaimReceived = $reimbursements->pluck('amount')->sum();
        $reimbursementClaimRejected = $reimbursements->where('rejected', 1)->pluck('amount')->sum();
        $reimbursementClaimProcessed = $reimbursements->where('reimbursed', 1)->pluck('amount')->sum();
        $reimbursementClaimPending = $reimbursementClaimReceived - $reimbursementClaimProcessed - $reimbursementClaimRejected;
        $data = [
          'month' => Carbon::createFromDate($year, $month)->startOfMonth()->addDays(1)->format('F Y'),
          'rawMonth' => $month,
          'rawYear' => $year,
          'received' => $reimbursementClaimReceived,
          'rejected' => $reimbursementClaimRejected,
          'processed' => $reimbursementClaimProcessed,
          'pending' => $reimbursementClaimPending,
        ];
        $summary->push($data);
      }
    } else {
      $summary = collect();
    }
    return view('client.transaction.Reimbursement.summary')->with('reimbursements', $summary);
  }

  public function reimbursementAdd(Request $request)
  {
//    return $request;
    $this->validate($request, [
      'employee' => 'required|integer',
      'expenseDate' => 'required|date',
      'expenseType' => 'required',
      'amount' => 'required|integer',
      'expenseBill' => 'required',
      'remarks' => 'required',
    ]);
    $reimbursement = new Reimbursement;
    $reimbursement->employee_id = $request->employee;
    $reimbursement->expenseDate = $request->expenseDate;
    $reimbursement->expenseType = $request->expenseType;
    $reimbursement->amount = $request->amount;

    $fileName = time() . '_' . $request->expenseBill->getClientOriginalName();
//    $request->expenseBill->move(public_path('uploads'), $fileName);
    $request->expenseBill->move(storage_path('app/public/uploads'), $fileName);

    $reimbursement->expenseBill = $fileName;

    $reimbursement->remarks = $request->remarks;
    $reimbursement->save();
    return redirect()->back();
  }

  public function reimbursementUpdate(Request $request)
  {
//    return $request;
    $this->validate($request, [
      'id' => 'required|integer',
      'employee' => 'required|integer',
      'expenseDate' => 'required|date',
      'expenseType' => 'required',
      'amount' => 'required|integer',
      'remarks' => 'required',
    ]);
    $reimbursement = Reimbursement::find($request->id);
    $reimbursement->employee_id = $request->employee;
    $reimbursement->expenseDate = $request->expenseDate;
    $reimbursement->expenseType = $request->expenseType;
    $reimbursement->amount = $request->amount;
    if ($request->expenseBill) {
      $fileName = time() . '_' . $request->expenseBill->getClientOriginalName();
//    $request->expenseBill->move(public_path('uploads'), $fileName);
      $request->expenseBill->move(storage_path('app/public/uploads'), $fileName);

      $reimbursement->expenseBill = $fileName;

    }
    $reimbursement->remarks = $request->remarks;
    $reimbursement->save();
    return redirect()->back();
  }

  public function reimburse(Request $request)
  {
    $reimbursement = Reimbursement::find($request->id);
    $reimbursement->reimbursed = 1;
    $reimbursement->reimbursedOn = $request->reimbursementDate;
    $reimbursement->reimbursedRemarks = $request->reimbursementRemarks;
    $reimbursement->save();
    return redirect()->back();
  }

  public function reject(Request $request)
  {
    $reimbursement = Reimbursement::find($request->id);
    $reimbursement->rejected = 1;
    $reimbursement->rejectedRemarks = $request->reimbursementRemarks;
    $reimbursement->save();
    return redirect()->back();
  }

  public function venueExpense()
  {
    if (\request()->month | \request()->year) {
      $year = \request()->year;
      $month = \request()->month;
      $start = Carbon::createFromDate($year, $month)->startOfMonth()->addDays(1);
      $end = Carbon::createFromDate($year, $month)->endOfMonth();
      $venues = Venue::whereBetween('venue_date', [$start, $end])->get();
    } else {
      $venues = \App\Venue::all()->sortByDesc('venue_date');
    }
    return view('client.transaction.venueexpense.index')->with('venues', $venues);
  }


  public function venueExpenseSummary()
  {

    if (Venue::all()->count()) {
      $start = \Carbon\Carbon::parse(Venue::all()->sortByDesc('venue_date')->first()->venue_date)->startOfYear();
      $end = \Carbon\Carbon::parse(Venue::all()->sortByDesc('venue_date')->last()->venue_date)->endOfYear();


      $interval = \DateInterval::createFromDateString('1 month');
      $period = new \DatePeriod($start, $interval, $end);
      $dates = collect();
      foreach ($period as $dt) {
        $dates->push($dt->format("Y-m"));
      }
      $summary = collect();
      foreach ($dates as $date) {
        $year = explode('-', $date)[0];
        $month = explode('-', $date)[1];
        $start = Carbon::createFromDate($year, $month)->startOfMonth()->addDays(1);
        $end = Carbon::createFromDate($year, $month)->endOfMonth();
        $venues = Venue::whereBetween('venue_date', [$start, $end])->get();
        $venueCost = 0;
        $venueFoodCost = 0;
        $venueStayCost = 0;
        $venueTravelCost = 0;
        $venueOtherCost = 0;
        foreach ($venues as $venue) {
          $venueCost += $venue->Expense->pluck('expense_amount')->sum();
          $venueStayCost += $venue->Expense->where('expense_type', 'stay')->pluck('expense_amount')->sum();
          $venueOtherCost += $venue->Expense->where('expense_type', 'other')->pluck('expense_amount')->sum();
          $venueFoodCost += $venue->Expense->where('expense_type', 'food')->pluck('expense_amount')->sum();
          $venueTravelCost += $venue->Expense->where('expense_type', 'travel')->pluck('expense_amount')->sum();
        }
        $data = [
          'month' => Carbon::createFromDate($year, $month)->startOfMonth()->addDays(1)->format('F Y'),
          'rawMonth' => $month,
          'rawYear' => $year,
          'totalVenues' => $venues->count(),
          'venueCost' => $venueCost,
          'foodCost' => $venueFoodCost,
          'otherCost' => $venueOtherCost,
          'travelCost' => $venueTravelCost,
          'stayCost' => $venueStayCost,
        ];
        $summary->push($data);
      }
    } else {
      $summary = collect();
    }
    return view('client.transaction.venueexpense.summary')->with('venues', $summary);
  }

  public function venueAdd(Request $request)
  {
    $venue = new Venue;
    $venue->venue_name = $request->Venue_Name;
    $venue->venue_location = $request->venueLocation;
    $venue->venue_date = $request->venueDate;
    $venue->save();
    return \redirect()->back();
  }

  public function venueExpenseAdd(Request $request)
  {
    $expense = new VenueExpenses;
    $expense->expense_name = $request->expenseName;
    $expense->expense_amount = $request->expenseAmount;
    $expense->expense_details = $request->expenseDetails;
    $expense->expense_type = $request->expenseType;
    $expense->venue_id = $request->id;
    if ($request->expenseBill) {
      $fileName = time() . '_venue_expense_bill' . $request->expenseBill->getClientOriginalName();
      $request->expenseBill->move(storage_path('app/public/uploads'), $fileName);
      $expense->expenseBill = $fileName;
    }

    $expense->save();
    return \redirect()->back();
  }

  public function venueExpenseEdit(Request $request)
  {
    $expense = VenueExpenses::find($request->id);
    $expense->expense_name = $request->expenseName;
    $expense->expense_amount = $request->expenseAmount;
    $expense->expense_details = $request->expenseDetails;
    $expense->expense_type = $request->expenseType;
    $expense->venue_id = $request->id;
    if ($request->expenseBill) {

      $fileName = time() . '_venue_expense_bill' . $request->expenseBill->getClientOriginalName();
      $request->expenseBill->move(storage_path('app/public/uploads'), $fileName);
      $expense->expenseBill = $fileName;
    }

    $expense->save();
    return \redirect()->back();
  }

  public function downloadChequesView()
  {
    return view('client.downloadCheque');
  }

  public function downloadCheques(Request $request)
  {
    if (PDC::whereDate('date_of_execution', '>=', Carbon::parse($request->from)->format('y-m-d'))->whereDate('date_of_execution', '<=', Carbon::parse($request->to)->format('y-m-d'))->count()) {
      $fileName = '(Total Amount:    ₹' . (PDC::whereDate('date_of_execution', '>=', Carbon::parse($request->from)->format('y-m-d'))->whereDate('date_of_execution', '<=', Carbon::parse($request->to)->format('y-m-d'))->get()->pluck('amount')->sum()) . '   )' . Carbon::parse($request->from)->format('d-m-y') . ' to ' . Carbon::parse($request->to)->format('d-m-y') . '-pdc-' . time() . rand() . '.xlsx';
      $url = (new FastExcel(PDC::whereDate('date_of_execution', '>=', Carbon::parse($request->from)->format('y-m-d'))->whereDate('date_of_execution', '<=', Carbon::parse($request->to)->format('y-m-d'))->get()))->export('excel/' . $fileName, function ($pdc) {
        return [
          'Cheque Date' => Carbon::parse($pdc->date_of_execution)->format('d-m-y'),
          'Cheque Number' => $pdc->cheque_no,
          'Amount' => strtoupper($pdc->amount),
          'MICR No.' => strtoupper($pdc->micr_number),
          'Branch Name' => strtoupper($pdc->branch_name),
          'Branch Address' => strtoupper($pdc->branch_address),
          'MAF No' => strtoupper($pdc->client->latestPackage->mafNo),
          'FTK' => strtoupper($pdc->client->latestPackage->fclpId),
          'Name' => strtoupper($pdc->client->name),
          'Cheque Status' => strtoupper($pdc->status),
        ];
      });
      $link = url('/excel/' . $fileName);
      return Redirect::to($link);
    } else {
      return redirect()->back();
    }
  }

  public function editCard(Request $request, $transactionId)
  {
    $payment = CardPayment::find($transactionId);
    if ($payment) {

      $payment->paymentDate = $request->paymentDate;
      $payment->amount = $request->paymentAmount;
      $payment->cardType = $request->paymentCardType;
      $payment->remarks = $request->paymentRemarks;
      $payment->save();
    }
    return redirect()->back();
  }


  public function editCheque(Request $request, $transactionId)
  {
    $payment = ChequePayment::find($transactionId);
    if ($payment) {
      $payment->paymentDate = $request->paymentDate;
      $payment->amount = $request->paymentAmount;
      $payment->chequeNumber = $request->paymentChequeNumber;
      $payment->chequeIssuer = $request->paymentChequeIssuer;
      $payment->chequeClearingBank = $request->paymentChequeClearingBank;
      $payment->remarks = $request->paymentRemarks;
      $payment->save();
    }
    return redirect()->back();
  }

  public function editCash(Request $request, $transactionId)
  {
    $payment = CashPayment::find($transactionId);
    if ($payment) {
      $payment->paymentDate = $request->paymentDate;
      $payment->amount = $request->paymentAmount;
      $payment->receiptNumber = $request->paymentReceiptNumber;
      $payment->remarks = $request->paymentRemarks;
      $payment->save();
    }
    return redirect()->back();
  }

  public function editOthers(Request $request, $transactionId)
  {
    $payment = OtherPayment::find($transactionId);
    if ($payment) {
      $payment->paymentDate = $request->paymentDate;
      $payment->amount = $request->paymentAmount;
      $payment->remarks = $request->paymentRemarks;
      $payment->modeOfPayment = $request->modeOfPayment;
      $payment->save();
    }
    return redirect()->back();
  }

//  public function createOther(Request $request,$clientId){
//    return $request;
//  }

  public function createOther(Request $request, $clientId)
  {
    $request->validate([
      'paymentReceivedOn' => 'required|date',
      'paymentAmount' => 'required|integer',
      'modeOfPayment' => 'required|string',
      'paymentCardRemarks' => 'required|string',
    ]);
    if ($request->has('paymentDownPayment') and $request->has('paymentAddon')) {
      notifyToast('error', 'OOPS!!', 'A payment can either be Addon or Down payment.');
    } else {
      DB::beginTransaction();
      try {
        $transaction = new OtherPayment();
        $transaction->client_id = $clientId;
        $transaction->paymentDate = $request->paymentReceivedOn;
        $transaction->amount = $request->paymentAmount;
        $transaction->modeOfPayment = $request->modeOfPayment;
        $transaction->remarks = $request->paymentCardRemarks;
        $transaction->save();
        if ($request->has('paymentForMonth')) {
          $co = 0;
          foreach ($request->paymentForMonth as $month) {
            $transactionMonth = new TransactionMonth;
////            return $request->paymentForMonth;
//            $transactionMonth->transaction_id  =$transaction->id;
//            $transactionMonth->paidMonth  =$month;
//            $transactionMonth->paidYear  = $request->paymentForYear[$co];
//            $transactionMonth->transactionType  = 'card';
//            $transactionMonth->save();
            $transactionMonth->create([
              'transaction_id' => $transaction->id,
              'paidMonth' => $month,
              'paidYear' => $request->paymentForYear[$co],
              'transactionType' => 'card',
            ]);
            $co++;
          }
        }
        DB::commit();
        notifyToast('success', 'Saved', 'Card Transaction Saved');
      } catch (\Exception $e) {
        DB::rollBack();
      }
    }
    return redirect()->back();

  }

  public function venueCancel(Request $request)
  {
    $venue = Venue::find($request->id);
    $venue->cancelled = 1;
    $venue->cancellationReason = $request->remarks;
    $venue->cancelledBy = Auth::user()->id;
    $venue->save();
    return \redirect()->back();
  }

  public function venueEdit(Request $request)
  {
    $venue = Venue::find($request->id);
    $venue->venue_name = $request->Venue_Name;
    $venue->venue_location = $request->venueLocation;
    $venue->venue_date = $request->venueDate;
    $venue->save();
    return \redirect()->back();
  }

  public function createAsf(Request $request, $clientId)
  {
    $asf = new AsfPayments;
    $asf->client_id = $clientId;
    if ($request->asfPaymentDate) {
      $asf->paymentDate = $request->asfPaymentDate;
    }
    $asf->amount = $request->asfAmount;
    $asf->year = $request->asfYear;
    if ($request->asfRemarks) {
      $asf->remarks = $request->asfRemarks;
    }
    if ($request->asfWaveOff) {
      $asf->waved_off = 1;
    }
    $asf->save();
    return \redirect()->back();
  }

  public function editAsf(Request $request, $clientId)
  {
//  return $request;
    $asf = AsfPayments::find($request->id);
//    $asf->client_id = $clientId;
    if ($request->asfPaymentDate) {
      $asf->paymentDate = $request->asfPaymentDate;
    }
    $asf->amount = $request->asfAmount;
    $asf->year = $request->asfYear;
    if ($request->asfRemarks) {
      $asf->remarks = $request->asfRemarks;
    }
    if ($request->asfWaveOff) {
      $asf->waved_off = 1;
    } else {
      $asf->waved_off = 0;
    }
    $asf->save();
    return \redirect()->back();
  }


  public function importHistoryDelete($importId, $bank)
  {
    if ($bank == 'axis') {
      try {
        $meta = AxisNachPaymentMeta::find($importId);
        foreach ($meta->payments as $payment) {
          $payment->delete();
          $meta->delete();
        }
        return '200';
      } catch (\Exception $e) {
        return '500';
      }
    }
  }

  public function importHistoryDownload($importId, $bank)
  {
    if ($bank == 'axis') {
      try {
        $meta = AxisNachPaymentMeta::find($importId);
        $file_name = $meta->file_name;
        $transactions = $meta->payments;
        return (new FastExcel($transactions))->download($file_name . '.xlsx');
      } catch (\Exception $e) {
        return \redirect()->back();
      }
    } elseif ($bank == 'yes') {
      try {
        $meta = YesNachPaymentMeta::find($importId);
        $file_name = $meta->file_name;
        $transactions = $meta->payments;
        return (new FastExcel($transactions))->download($file_name . '.xlsx');
      } catch (\Exception $e) {
        return \redirect()->back();
      }
    }
  }

}


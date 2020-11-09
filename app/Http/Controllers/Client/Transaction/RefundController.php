<?php

namespace App\Http\Controllers\Client\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\RefundRequests as RefundRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RefundController extends Controller
{
  public function refund(RefundRequest $rr, Request $request){
    $rr->client_id = $request->id;
    $rr->added_by = Auth::id();
    $rr->amount = $request->amount;
    $rr->reason = $request->reason;
    $rr->through = $request->through;
    $rr->refund_date = $request->refund_date;
    $rr->save();
    return redirect()->back();

  }

  public function refundRequests(){
    return view('client.refund');
  }

  public function acceptRefundRequest(Request $request){
    $rr = RefundRequest::find($request->refund_request);
    $rr->accepted_denied = 1;
    $rr->accepted_denied_by = Auth::id();
    $rr->accepted_denied_remarks = $request->accepted_denied_remarks;
    $rr->accepted_denied_datetime = Carbon::now();
    $rr->save();
    return redirect()->back();
  }

  public function denyRefundRequest(Request $request){
    $rr = RefundRequest::find($request->refund_request);
    $rr->accepted_denied = 2;
    $rr->accepted_denied_by = Auth::id();
    $rr->accepted_denied_remarks = $request->accepted_denied_remarks;
    $rr->accepted_denied_datetime = Carbon::now();
    $rr->save();

    if($request->change_client_status == 'on'){
      if($request->status == 'cancel'){
        $rr->client->cancelled = 1;
        $rr->client->remarks = $request->approval_accounts_remarks;
        $rr->client->save();
        $rr->accepted_denied_client_status_changed = 'Cancelled';
        $rr->save();
      }elseif($request->status == 'forfiet'){
        $rr->client->forfieted = 1;
        $rr->client->remarks = $request->approval_accounts_remarks;
        $rr->client->save();
        $rr->accepted_denied_client_status_changed = 'Forfieted';
        $rr->save();
      }
    }
    return redirect()->back();
  }


  public function approveRefundRequest(Request $request){
    $rr = RefundRequest::find($request->refund_request);
    $rr->approved_rejected = 1;
    $rr->approved_rejected_by = Auth::id();
    $rr->approved_rejected_remarks = $request->approved_rejected_remarks;
    $rr->approved_rejected_datetime = Carbon::now();
    $rr->approved_rejected_amount = $request->amount;
    $rr->save();
    return redirect()->back();
  }

  public function rejectRefundRequest(Request $request){
    $rr = RefundRequest::find($request->refund_request);
    $rr->approved_rejected = 2;
    $rr->approved_rejected_by = Auth::id();
    $rr->approved_rejected_remarks = $request->approved_rejected_remarks;
    $rr->approved_rejected_datetime = Carbon::now();
    $rr->save();

    if($request->change_client_status == 'on'){
      if($request->status == 'cancel'){
        $rr->client->cancelled = 1;
        $rr->client->save();
        $rr->approved_rejected_client_status_changed = 'Cancelled';
        $rr->save();
      }elseif($request->status == 'forfiet'){
        $rr->client->forfieted = 1;
        $rr->client->save();
        $rr->approved_rejected_client_status_changed = 'Forfieted';
        $rr->save();
      }
    }
    return redirect()->back();
  }

  public function approveAccountsRefundRequest(Request $request){
    $rr = RefundRequest::find($request->refund_request);

    $rr->approval_accounts_by = Auth::id();
    $rr->approval_accounts_remarks = $request->approval_accounts_remarks;
    $rr->approval_accounts_datetime = Carbon::now();
    $rr->approval_accounts_amount = $request->amount;

    $rr->date_of_payment = $request->date_of_payment;
    $rr->mode_of_payment = $request->mode_of_payment;
    $rr->last_four_digits = $request->last_four_digits;
    $rr->card_name = $request->card_name;
    $rr->bank_name = $request->bank_name;
    $rr->cheque_number = $request->cheque_number;
    $rr->save();

    if($request->change_client_status == 'on'){
      if($request->status == 'cancel'){
        $rr->client->cancelled = 1;
        $rr->client->save();
        $rr->approval_accounts_client_status_changed = 'Cancelled';
        $rr->save();
      }elseif($request->status == 'forfiet'){
        $rr->client->forfieted = 1;
        $rr->client->save();
        $rr->approval_accounts_client_status_changed = 'Forfieted';
        $rr->save();
      }
    }
    return redirect()->back();
  }
}

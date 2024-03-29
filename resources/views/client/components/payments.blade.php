<div class="col-lg-12 col-12">
  @if($user->employee)

    <h3 class="text-center">
      @if(!request()->type)
        Regular Payments
        <a href="{{ route('view.client',['slug'=>$client->slug,'show'=>'payments','type'=>'asf']) }}">
          <button class="btn btn-primary btn-sm">ASF Payments</button>
        </a>
        <a href="{{ route('view.client',['slug'=>$client->slug,'show'=>'payments','type'=>'addOn']) }}">
          <button class="btn btn-primary btn-sm">
            Add On Payments
          </button>
        </a>
      @elseif(request()->type == 'asf')
        ASF Payments
        <a href="{{ route('view.client',['slug'=>$client->slug,'show'=>'payments']) }}">
          <button class="btn btn-primary btn-sm">
            Regular Payments
          </button>
        </a>
        <a href="{{ route('view.client',['slug'=>$client->slug,'show'=>'payments','type'=>'addOn']) }}">
          <button class="btn btn-primary btn-sm">
            Add On Payments
          </button>
        </a>
      @elseif(request()->type == 'addOn')
        Add On Payments
        <a href="{{ route('view.client',['slug'=>$client->slug,'show'=>'payments']) }}">
          <button class="btn btn-primary btn-sm">
            Regular Payments
          </button>
        </a>
        <a href="{{ route('view.client',['slug'=>$client->slug,'show'=>'payments','type'=>'asf']) }}">
          <button class="btn btn-primary btn-sm">ASF Payments</button>
        </a>
      @endif
    </h3>
  @endif
  @php
    $totalTransactions = collect();
      if($client->cashPayments->count()){
          foreach($client->CashPayments->where('isAddon',0) as $ca){
            $totalTransactions->push(['date'=>$ca->paymentDate,'amount'=>$ca->amount,'remarks'=>$ca->remarks,'mode'=>'Cash','dp'=>$ca->isDp, 'breather'=>0]);
          }
      }
      if($client->cardPayments->count()){
          foreach($client->CardPayments->where('isAddon',0) as $cad){
            $totalTransactions->push(['date'=>$cad->paymentDate,'amount'=>$cad->amount,'remarks'=>$cad->remarks,'mode'=>'Card','dp'=>$cad->isDp, 'breather'=>0]);
          }
      }
      if($client->chequePayments->count()){
          foreach($client->chequePayments->where('isAddon',0) as $che){
            $totalTransactions->push(['date'=>$che->paymentDate,'amount'=>$che->amount,'remarks'=>$che->remarks,'mode'=>'Cheque','dp'=>$che->isDp, 'breather'=>0]);
          }
      }
      if($client->otherPayments->count()){
          foreach($client->otherPayments->where('isAddon',0) as $oth){
            $totalTransactions->push(['date'=>$oth->paymentDate,'amount'=>$oth->amount,'remarks'=>$oth->remarks,'mode'=>$oth->modeOfPayment,'dp'=>$oth->isDp, 'breather'=>$oth->isBreather]);
          }
      }

      if($client->id != 590){
        if($client->AxisPayments->count()){
            foreach($client->AxisPayments as $axp){
              if($axp->status_description == 'success' or $axp->status_description == 'SUCCESS' or $axp->status_description == 'Success'){
                $totalTransactions->push(['date'=>$axp->date_of_transaction,'amount'=>$axp->amount,'remarks'=>$axp->reason_description,'mode'=>'AXIS NACH','dp'=>'', 'breather'=>0]);
              }
            }
        }
        if($client->id == 786){
          $payAxi = \App\Client\Transaction\AxisNachPayment::where('client_id',590)->get();
          foreach($payAxi as $paAx){
            if($paAx->status_description == 'success' or $paAx->status_description == 'SUCCESS' or $paAx->status_description == 'Success'){
                $totalTransactions->push(['date'=>$paAx->date_of_transaction,'amount'=>$paAx->amount,'remarks'=>$paAx->reason_description,'mode'=>'AXIS NACH','dp'=>'', 'breather'=>0]);
              }
          }
        }
      }

      if($client->YesPayments->count()){
          foreach($client->YesPayments as $yep){
            if($yep->STATUS == 'ACCEPTED'){
              $totalTransactions->push(['date'=>$yep->VALUE_DATE,'amount'=>$yep->AMOUNT,'remarks'=>$yep->REASON_CODE,'mode'=>'YES NACH','dp'=>'']);
            }
          }
      }


  $addOnTransactions = collect();
if($client->cashPayments->count()){
foreach($client->CashPayments->where('isAddon', 1) as $ca){
$addOnTransactions->push(['date'=>$ca->paymentDate,'amount'=>$ca->amount,'remarks'=>$ca->remarks,'mode'=>'Cash','dp'=>$ca->isDp]);
}
}
if($client->cardPayments->count()){
foreach($client->CardPayments->where('isAddon', 1) as $cad){
$addOnTransactions->push(['date'=>$cad->paymentDate,'amount'=>$cad->amount,'remarks'=>$cad->remarks,'mode'=>'Card','dp'=>$cad->isDp]);
}
}
if($client->chequePayments->count()){
foreach($client->chequePayments->where('isAddon', 1) as $che){
$addOnTransactions->push(['date'=>$che->paymentDate,'amount'=>$che->amount,'remarks'=>$che->remarks,'mode'=>'Cheque','dp'=>$che->isDp]);
}
}
if($client->otherPayments->count()){
foreach($client->otherPayments->where('isAddon', 1) as $oth){
$addOnTransactions->push(['date'=>$oth->paymentDate,'amount'=>$oth->amount,'remarks'=>$oth->remarks,'mode'=>$oth->modeOfPayment,'dp'=>$oth->isDp]);
}
}
  @endphp
  @if(!request()->type)

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Summary</h4>
          </div>
          <div class="card-content">
            <div class="card-body card-dashboard">
              <strong>FCLP Cost</strong>: {{ inr($client->latestPackage->productCost) }} <br>
              <strong>Downpayment</strong>: {{ inr($client->downPayment) }} <br>
              <strong>Total Payment Done(Including DP)</strong>: {{ inr($totalTransactions->where('breather',0)->pluck('amount')->sum()) }} <br>
              <strong>Pending Payment</strong>: {{ inr($client->latestPackage->productCost - $totalTransactions->pluck('amount')->sum())  }} <br>
              <strong>Add On Payment</strong>: {{ inr($client->addOnPayment) }} <br>
              <strong>Breather Charges</strong>: {{ inr($client->otherPayments ? $client->otherPayments->where('isBreather',1)->sum('amount') : '0') }}
              <br>
              @if($user->employee)
                @if($client->refundRequest)
                  @if($client->refundRequest->approval_accounts_by)
                    Refunded Amount: {{ $client->refundRequest->approval_accounts_amount }}
                  @endif
                @endif
                <br>
                <hr>
                <strong>
                  @if($client->refundRequest)
                    Refund Request Added On: {{ $client->refundRequest->refund_date }}
                    @if($client->refundRequest->approved_rejected == 2)
                      (Denied By Manager | Remarks: {{ $client->refundRequest->approved_rejected_remarks }})
                    @elseif($client->refundRequest->accepted_denied == 2)
                      (Denied By MRD | Remarks: {{ $client->refundRequest->accepted_denied_remarks }})
                    @elseif($client->refundRequest->approval_accounts_by)
                      (Approved By Accounts | Amount: {{ $client->refundRequest->approval_accounts_amount }} |
                      Remarks: {{ $client->refundRequest->approval_accounts_remarks }})
                    @endif
                    {{--        {{ $client->refundRequest }}--}}
                  @endif

                  @endif
                </strong>
            </div>
          </div>
        </div>

      </div>


      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Total Payments</h4>
          </div>
          <div class="card-content">
            <div class="card-body card-dashboard">
              <div class="table-responsive">
                <table class="table zero-configuration">
                  <thead>
                  <tr>
                    <th>Payment Date</th>
                    <th>Amount</th>
                    <th>Mode Of payment</th>
                    <th>Remarks</th>
                    <th>DP</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($totalTransactions as $transaction)
                    <tr>
                      <td>{{ $transaction['date'] }}</td>
                      <td>{{ $transaction['amount'] }}</td>
                      <td>{{ $transaction['mode'] }}</td>
                      <td>{{ $transaction['remarks'] }}</td>
                      <td>
{{--                        {{ $transaction['dp'] == 1 ? 'Downpayment' : 'EMI' }}--}}
                                                @if(array_key_exists('breather', $transaction))
                                                  @if($transaction['breather'] == 1)
                                                  Breather
                                                    @endif
                                                @elseif($transaction['dp'] == 1)
                                                  Downpayment
                                                @else
                                                  EMI
                                                @endif
                      </td>
                    </tr>
                  @endforeach
                  </tbody>

                  <tfoot>
                  <tr>
                    <th>Payment Date</th>
                    <th>Amount</th>
                    <th>Mode Of payment</th>
                    <th>Remarks</th>
                    <th>DP</th>
                  </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>

      </div>

      @if($client->cardPayments->count())
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Card Payments</h4>
            </div>
            <div class="card-content">
              <div class="card-body card-dashboard">
                <div class="table-responsive">
                  <table class="table zero-configuration">
                    <thead>
                    <tr>
                      <th>Payment Date</th>
                      <th>Amount</th>
                      <th>Card Type</th>
                      <th>Last Four Digits</th>
                      <th>Remarks</th>
                      <th>DP</th>
                      @if($user->employee)
                        <th>Action</th>
                      @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($client->CardPayments as $cardPayment)
                      <tr>
                        <td>{{ $cardPayment->paymentDate }}</td>
                        <td>{{ $cardPayment->amount }}</td>
                        <td>{{ $cardPayment->cardType }}</td>
                        <td>{{ $cardPayment->cardLastFourDigits }}</td>
                        <td>{{ $cardPayment->remarks }}</td>
                        <td>{{ $cardPayment->isDp == 1 ? 'Downpayment' : 'EMI' }}</td>
                        @if($user->employee)

                          <td>
                            <button class="btn btn-primary btn-sm" data-toggle="modal"
                                    data-target="#editCardPayment{{$cardPayment->id}}">Edit
                            </button>
                            <div class="modal fade" id="editCardPayment{{$cardPayment->id}}" tabindex="-1" role="dialog"
                                 aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Edit Card Transaction</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <form
                                    action="{{ route('edit.transaction.card',['transactionId'=>$cardPayment->id]) }}"
                                    method="POST">
                                    @csrf
                                    <div class="modal-body">
                                      <div class="row">
                                        <div class="col-md-12">
                                          <label for="paymentDate">Payment Date</label>
                                          <input type="date" class="form-control" name="paymentDate"
                                                 value="{{ $cardPayment->paymentDate }}" required>
                                        </div>
                                        <div class="col-md-12">
                                          <label for="paymentAmount">Amount</label>
                                          <input type="number" class="form-control" name="paymentAmount"
                                                 value="{{ $cardPayment->amount }}" required>
                                        </div>
                                        <div class="col-md-12">
                                          <label for="paymentCardType">Card Type</label>
                                          <input type="text" class="form-control" name="paymentCardType"
                                                 value="{{ $cardPayment->cardType }}" required>
                                        </div>
                                        <div class="col-md-12">
                                          <label for="paymentRemarks">Remarks</label>
                                          <input type="text" class="form-control" name="paymentRemarks"
                                                 value="{{ $cardPayment->remarks }}" required>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close
                                      </button>
                                      <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                  </form>
                                </div>
                              </div>
                            </div>

                          </td>
                        @endif
                      </tr>
                    @endforeach
                    </tbody>

                    <tfoot>
                    <tr>
                      <th>Payment Date</th>
                      <th>Amount</th>
                      <th>Card Type</th>
                      <th>Last Four Digits</th>
                      <th>Remarks</th>
                      <th>DP</th>
                      @if($user->employee)

                        <th>Action</th>
                      @endif
                    </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>

        </div>
      @endif


      @if($client->cashPayments->count())
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Cash Payments</h4>
            </div>
            <div class="card-content">
              <div class="card-body card-dashboard">
                <div class="table-responsive">
                  <table class="table zero-configuration">
                    <thead>
                    <tr>
                      <th>Payment Date</th>
                      <th>Amount</th>
                      <th>Receipt Number</th>
                      <th>Remarks</th>
                      <th>DP</th>
                      @if($user->employee)

                        <th>Action</th>
                      @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($client->cashPayments as $cashPayment)
                      <tr>
                        <td>{{ $cashPayment->paymentDate }}</td>
                        <td>{{ $cashPayment->amount }}</td>
                        <td>{{ $cashPayment->receiptNumber }}</td>
                        <td>{{ $cashPayment->remarks }}</td>
                        @if($user->employee)

                          <td>{{ $cashPayment->isDp ? 'Downpayment' : 'EMI'}}</td>
                        @endif
                        <td>
                          <button class="btn btn-primary btn-sm" data-toggle="modal"
                                  data-target="#editCashPayment{{$cashPayment->id}}">Edit
                          </button>
                          <div class="modal fade" id="editCashPayment{{$cashPayment->id}}" tabindex="-1" role="dialog"
                               aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLongTitle">Edit Cash Transaction</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <form action="{{ route('edit.transaction.cash',['transactionId'=>$cashPayment->id]) }}"
                                      method="POST">
                                  @csrf
                                  <div class="modal-body">
                                    <div class="row">
                                      <div class="col-md-12">
                                        <label for="paymentDate">Payment Date</label>
                                        <input type="date" class="form-control" name="paymentDate"
                                               value="{{ $cashPayment->paymentDate }}" required>
                                      </div>
                                      <div class="col-md-12">
                                        <label for="paymentAmount">Amount</label>
                                        <input type="number" class="form-control" name="paymentAmount"
                                               value="{{ $cashPayment->amount }}" required>
                                      </div>
                                      <div class="col-md-12">
                                        <label for="paymentReceiptNumber">Receipt Number</label>
                                        <input type="text" class="form-control" name="paymentReceiptNumber"
                                               value="{{ $cashPayment->receiptNumber }}" required>
                                      </div>
                                      <div class="col-md-12">
                                        <label for="paymentRemarks">Remarks</label>
                                        <input type="text" class="form-control" name="paymentRemarks"
                                               value="{{ $cashPayment->remarks }}" required>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                  </div>
                                </form>
                              </div>
                            </div>
                          </div>

                        </td>

                      </tr>
                    @endforeach
                    </tbody>

                    <tfoot>
                    <tr>
                      <th>Payment Date</th>
                      <th>Amount</th>
                      <th>Receipt Number</th>
                      <th>Remarks</th>
                      <th>DP</th>
                      @if($user->employee)

                        <th>Action</th>
                      @endif
                    </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>

        </div>
      @endif


      @if($client->chequePayments->count())
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Cheque Payments</h4>
            </div>
            <div class="card-content">
              <div class="card-body card-dashboard">
                <div class="table-responsive">
                  <table class="table zero-configuration">
                    <thead>
                    <tr>
                      <th>Payment Date</th>
                      <th>Amount</th>
                      <th>Cheque Number</th>
                      <th>Cheque Issuer</th>
                      <th>Cheque Clearing Bank</th>
                      <th>Remarks</th>
                      <th>DP</th>
                      @if($user->employee)

                        <th>Action</th>
                      @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($client->ChequePayments as $chequePayment)
                      <tr>
                        <td>{{ $chequePayment->paymentDate }}</td>
                        <td>{{ $chequePayment->amount }}</td>
                        <td>{{ $chequePayment->chequeNumber }}</td>
                        <td>{{ $chequePayment->chequeIssuer }}</td>
                        <td>{{ $chequePayment->chequeClearingBank }}</td>
                        <td>{{ $chequePayment->remarks }}</td>
                        <td>{{ $chequePayment->isDp == 1 ? 'Downpayment' : 'EMI' }}</td>
                        @if($user->employee)

                          <td>
                            <button class="btn btn-primary btn-sm" data-toggle="modal"
                                    data-target="#editChequePayment{{$chequePayment->id}}">Edit
                            </button>
                            <div class="modal fade" id="editChequePayment{{$chequePayment->id}}" tabindex="-1"
                                 role="dialog" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Edit Cheque Transaction</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <form
                                    action="{{ route('edit.transaction.cheque',['transactionId'=>$chequePayment->id]) }}"
                                    method="POST">
                                    @csrf
                                    <div class="modal-body">
                                      <div class="row">
                                        <div class="col-md-12">
                                          <label for="paymentDate">Payment Date</label>
                                          <input type="date" class="form-control" name="paymentDate"
                                                 value="{{ $chequePayment->paymentDate }}" required>
                                        </div>
                                        <div class="col-md-12">
                                          <label for="paymentAmount">Amount</label>
                                          <input type="number" class="form-control" name="paymentAmount"
                                                 value="{{ $chequePayment->amount }}" required>
                                        </div>
                                        <div class="col-md-12">
                                          <label for="paymentChequeNumber">Cheque Number</label>
                                          <input type="text" class="form-control" name="paymentChequeNumber"
                                                 value="{{ $chequePayment->chequeNumber }}" required>
                                        </div>
                                        <div class="col-md-12">
                                          <label for="paymentChequeIssuer">Cheque Issuer</label>
                                          <input type="text" class="form-control" name="paymentChequeIssuer"
                                                 value="{{ $chequePayment->chequeIssuer }}" required>
                                        </div>
                                        <div class="col-md-12">
                                          <label for="paymentChequeClearingBank">Cheque Clearing Bank</label>
                                          <input type="text" class="form-control" name="paymentChequeClearingBank"
                                                 value="{{ $chequePayment->chequeClearingBank }}" required>
                                        </div>
                                        <div class="col-md-12">
                                          <label for="paymentRemarks">Remarks</label>
                                          <input type="text" class="form-control" name="paymentRemarks"
                                                 value="{{ $chequePayment->remarks }}" required>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close
                                      </button>
                                      <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                  </form>
                                </div>
                              </div>
                            </div>

                          </td>

                        @endif

                      </tr>
                    @endforeach
                    </tbody>

                    <tfoot>
                    <tr>
                      <th>Payment Date</th>
                      <th>Amount</th>
                      <th>Cheque Number</th>
                      <th>Cheque Issuer</th>
                      <th>Cheque Clearing Bank</th>
                      <th>Remarks</th>
                      <th>DP</th>
                      @if($user->employee)

                        <th>Action</th>
                      @endif
                    </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>

        </div>
      @endif

      @if($client->otherPayments->count())
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Other Payments</h4>
            </div>
            <div class="card-content">
              <div class="card-body card-dashboard">
                <div class="table-responsive">
                  <table class="table zero-configuration">
                    <thead>
                    <tr>
                      <th>Payment Date</th>
                      <th>Amount</th>
                      <th>Mode Of Payment</th>
                      <th>Remarks</th>
                      <th>DP</th>
                      @if($user->employee)

                        <th>Action</th>
                      @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($client->otherPayments as $otherPayment)
                      <tr>
                        <td>{{ $otherPayment->paymentDate }}</td>
                        <td>{{ $otherPayment->amount }} <strong>{{$otherPayment->isBreather ? '(Breather Charges)':''}}</strong></td>
                        <td>{{ $otherPayment->modeOfPayment }}</td>
                        <td>{{ $otherPayment->remarks }}</td>
                        <td>{{ $otherPayment->idDp }}</td>
                        @if($user->employee)

                          <td>
                            <button class="btn btn-primary btn-sm" data-toggle="modal"
                                    data-target="#editOtherPayment{{$otherPayment->id}}">Edit
                            </button>
                            <div class="modal fade" id="editOtherPayment{{$otherPayment->id}}" tabindex="-1"
                                 role="dialog" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Edit Cheque Transaction</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <form
                                    action="{{ route('edit.transaction.others',['transactionId'=>$otherPayment->id]) }}"
                                    method="POST">
                                    @csrf
                                    <div class="modal-body">
                                      <div class="row">
                                        <div class="col-md-12">
                                          <label for="paymentDate">Payment Date</label>
                                          <input type="date" class="form-control" name="paymentDate"
                                                 value="{{ $otherPayment->paymentDate }}" required>
                                        </div>
                                        <div class="col-md-12">
                                          <label for="paymentAmount">Amount</label>
                                          <input type="number" class="form-control" name="paymentAmount"
                                                 value="{{ $otherPayment->amount }}" required>
                                        </div>
                                        <div class="col-md-12">
                                          <label for="paymentChequeNumber">Mode Of Payment</label>
                                          <input type="text" class="form-control" name="modeOfPayment"
                                                 value="{{ $otherPayment->modeOfPayment }}" required>
                                        </div>

                                        <div class="col-md-12">
                                          <label for="paymentRemarks">Remarks</label>
                                          <input type="text" class="form-control" name="paymentRemarks"
                                                 value="{{ $otherPayment->remarks }}" required>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close
                                      </button>
                                      <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                  </form>
                                </div>
                              </div>
                            </div>

                          </td>

                        @endif

                      </tr>
                    @endforeach
                    </tbody>

                    <tfoot>
                    <tr>
                      <th>Payment Date</th>
                      <th>Amount</th>
                      <th>Mode Of Payment</th>
                      <th>Remarks</th>
                      <th>DP</th>
                      @if($user->employee)
                        <th>Action</th>
                      @endif
                    </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>

        </div>
      @endif


      @if($client->AxisPayments->count())
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Axis NACH Payments(UMRN: {{  $client->AxisPayments->first()->umrn }} |
                Success: {{ $client->axisPayments->where('status_description','Success')->pluck('amount')->sum()   }} |
                Failed: {{ $client->AxisPayments->where('status_description','INITIAL REJECTION')->pluck('amount')->sum()   }}
                |
                @if($client->axisMis->count())
                  From {{ \Carbon\Carbon::parse($client->axisMis->last()->STARTDATE)->format('d-m-Y') }} To
                  {{ \Carbon\Carbon::parse($client->axisMis->last()->ENDDATE)->format('d-m-Y') }}
                @endif
                )
              </h4>
            </div>
            <div class="card-content">
              <div class="card-body card-dashboard">
                <div class="table-responsive">
                  <table class="table zero-configuration">
                    <thead>
                    <tr>
                      <th>Payment Date</th>
                      <th>Amount</th>
                      <th>Status</th>
                      <th>Reason</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($client->axisPayments as $axisPayment)
                      <tr>
                        <td>{{ $axisPayment->date_of_transaction }}</td>
                        <td>{{ $axisPayment->amount }}</td>
                        <td>{{ $axisPayment->status_description }}</td>
                        <td>{{ $axisPayment->reason_description }}</td>
                      </tr>
                    @endforeach
                    </tbody>

                    <tfoot>
                    <tr>
                      <th>Payment Date</th>
                      <th>Amount</th>
                      <th>Status</th>
                      <th>Reason</th>
                    </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>

        </div>
      @endif


      @if($client->YesPayments->count())
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Yes NACH Payments(Reference: {{  $client->YesPayments->first()->ITEM_REFERENCE }} |
                Success: {{ $client->YesPayments->where('STATUS','ACCEPTED')->pluck('AMOUNT')->sum()   }} |
                Failed: {{ $client->YesPayments->where('STATUS','RETURNED')->pluck('AMOUNT')->sum()   }})</h4>
            </div>
            <div class="card-content">
              <div class="card-body card-dashboard">
                <div class="table-responsive">
                  <table class="table zero-configuration">
                    <thead>
                    <tr>
                      <th>Payment Date</th>
                      <th>Amount</th>
                      <th>Status</th>
                      <th>Reason</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($client->yesPayments as $yesPayment)
                      <tr>
                        <td>{{ $yesPayment->VALUE_DATE }}</td>
                        <td>{{ $yesPayment->AMOUNT }}</td>
                        <td>{{ $yesPayment->STATUS }}</td>
                        <td>{{ $yesPayment->REASON_CODE }}</td>
                      </tr>
                    @endforeach
                    </tbody>

                    <tfoot>
                    <tr>
                      <th>Payment Date</th>
                      <th>Amount</th>
                      <th>Status</th>
                      <th>Reason</th>

                    </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>

        </div>
      @endif

      {{--    @foreach($client->TimelineActivity->sortByDesc('created_at') as $activity)--}}
      {{--      <div class="col-md-12">--}}
      {{--        <div class="card">--}}
      {{--          <div class="card-body">--}}
      {{--            <div class="d-flex justify-content-start align-items-center mb-1">--}}
      {{--              <div class="avatar mr-1">--}}
      {{--                <img src="{{ avatar($activity->User->name) }}" alt="avtar img holder" height="45"--}}
      {{--                     width="45">--}}
      {{--              </div>--}}
      {{--              <div class="user-page-info">--}}
      {{--                <h6 class="mb-0">{{ $activity->User->name }}</h6>--}}
      {{--                <span class="font-small-2">{{readableDate($activity->created_at) }}</span>--}}
      {{--              </div>--}}
      {{--            </div>--}}
      {{--            <p>{{ $activity->title }}</p>--}}
      {{--            {!! $activity->body !!}--}}
      {{--            <div class="d-flex justify-content-start align-items-center mb-1">--}}

      {{--              <p class="ml-auto d-flex align-items-center">--}}
      {{--                <i class="feather icon-message-square font-medium-2 mr-50"></i>{{ $activity->comments->count() }}--}}
      {{--              </p>--}}
      {{--            </div>--}}
      {{--            @forelse($activity->comments as $comment)--}}
      {{--              <div class="d-flex justify-content-start align-items-center mb-1">--}}
      {{--                <div class="avatar mr-50">--}}
      {{--                  <img src="{{ avatar($comment->User->name) }}" alt="Avatar" height="30" width="30">--}}
      {{--                </div>--}}
      {{--                <div class="user-page-info">--}}
      {{--                  <h6 class="mb-0">{{ $comment->User->name }} @ <span class="font-small-2">{{ readableDate($comment->created_at) }}</span></h6>--}}
      {{--                  <span class="font-small-4">{{ $comment->body }}</span>--}}
      {{--                  <span class="font-small-1"></span>--}}
      {{--                </div>--}}
      {{--              </div>--}}
      {{--            @empty--}}
      {{--              No Comments--}}
      {{--            @endforelse--}}
      {{--            <form action="{{ route('create.client.timelineComment',['activityId'=>$activity->id]) }}" method="POST">--}}
      {{--              @csrf--}}
      {{--              <fieldset class="form-label-group mb-50">--}}
      {{--                <textarea class="form-control" id="label-textarea3" rows="3" placeholder="Add Comment" name="activityComment"></textarea>--}}
      {{--                <label for="label-textarea3">Add Comment</label>--}}
      {{--              </fieldset>--}}
      {{--              <button type="submit" class="btn btn-sm btn-primary">Save Comment</button>--}}
      {{--            </form>--}}
      {{--          </div>--}}
      {{--        </div>--}}
      {{--      </div>--}}
      {{--    @endforeach--}}

    </div>
  @elseif(request()->type == 'asf')
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">ASF Payments
              <button class="btn btn-primary btn-sm"  data-toggle="modal" data-target="#addAsfPayment"><i class="fa fa-plus"></i></button>
            </h4>
          </div>
          <div class="card-content">
            <div class="card-body card-dashboard">
              Total Paid: {{ $client->AsfPayments->where('waved_off',0)->pluck('amount')->sum() }} <br>
              Waved Off: {{ $client->AsfPayments->where('waved_off',1)->pluck('amount')->sum() }} <br>

              <br>
              <div class="table-responsive">
                <table class="table zero-configuration">
                  <thead>
                  <tr>
                    <th>Payment Date</th>
                    <th>Amount</th>
                    <th>Year</th>
                    <th>Remarks</th>
                    <th>Waved Off</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($client->AsfPayments as $asfPayment)
                    <tr>
                      <td>{{ $asfPayment->paymentDate }}</td>
                      <td>{{ $asfPayment->amount }}</td>
                      <td>{{ $asfPayment->year }}</td>
                      <td>{{ $asfPayment->remarks }}</td>
                      <td>{{ $asfPayment->waved_off == 1 ? 'Waved Off' : 'No' }}</td>
                      <td>
                        <button class="btn btn-primary btn-sm"  data-toggle="modal" data-target="#editAsfPayment{{ $asfPayment->id }}"><i class="fa fa-edit"></i></button>
                        <div class="modal fade" id="editAsfPayment{{ $asfPayment->id }}" tabindex="-1" role="dialog" aria-labelledby="editAsfPayment{{ $asfPayment->id }}Title"
                             aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Edit ASF Paymnet</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <form action="{{ route('edit.transaction.asf',['clientId'=>$client->id]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{$asfPayment->id}}">
                                <div class="modal-body">
                                  <div class="row">
                                    <div class="col-md-12">
                                      <label for="asfPaymentDate">Payment Date</label>
                                      <input type="date" name="asfPaymentDate" id="asfPaymentDate" class="form-control" value="{{ $asfPayment->paymentDate }}">
                                    </div>
                                    <div class="col-md-12">
                                      <label for="asfAmount">Amount</label>
                                      <input type="number" name="asfAmount" id="asfAmount" class="form-control" required value="{{ $asfPayment->amount }}">
                                    </div>
                                    <div class="col-md-12">
                                      <label for="asfYear">Paid For Year</label>
                                      <select name="asfYear" id="asfYear" required class="form-control">
                                        <option value="2020"  {{$asfPayment->year == '2020' ? 'selected': ''}}>2020</option>
                                        <option value="2021" {{$asfPayment->year == '2021' ? 'selected': ''}}>2021</option>
                                        <option value="2022" {{$asfPayment->year == '2022' ? 'selected': ''}}>2022</option>
                                        <option value="2023" {{$asfPayment->year == '2023' ? 'selected': ''}}>2023</option>
                                        <option value="2024" {{$asfPayment->year == '2024' ? 'selected': ''}}>2024</option>
                                        <option value="2025" {{$asfPayment->year == '2025' ? 'selected': ''}}>2025</option>
                                      </select>
                                    </div>
                                    <div class="col-md-12">
                                      <label for="asfRemarks">Remarks</label>
                                      <textarea class="form-control" name="asfRemarks" id="asfRemarks" cols="30" rows="10" required>{{ $asfPayment->remarks }}</textarea>
                                    </div>
                                    <div class="col-md-12">
                                      <label for="asfWaveOff">Wave Off</label>
                                      <input type="checkbox" name="asfWaveOff" id="asfWaveOff" {{ $asfPayment->waved_off == 1 ? 'checked' : ''}}>
                                    </div>

                                  </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                  <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>

                      </td>
                    </tr>
                  @endforeach
                  </tbody>

                  <tfoot>
                  <tr>
                    <th>Payment Date</th>
                    <th>Amount</th>
                    <th>Year</th>
                    <th>Remarks</th>
                    <th>Waved Off</th>
                    <th>Action</th>
                  </tr>
                  </tfoot>
                </table>
              </div>

            </div>
          </div>
        </div>

      </div>
    </div>
    <div class="modal fade" id="addAsfPayment" tabindex="-1" role="dialog" aria-labelledby="addAsfPaymentTitle"
         aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">ADD ASF Paymnet</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="{{ route('create.transaction.asf',['clientId'=>$client->id]) }}" method="POST">
            @csrf
            <div class="modal-body">
              <div class="row">
                <div class="col-md-12">
                  <label for="asfPaymentDate">Payment Date</label>
                  <input type="date" name="asfPaymentDate" id="asfPaymentDate" class="form-control">
                </div>
                <div class="col-md-12">
                  <label for="asfAmount">Amount</label>
                  <input type="number" name="asfAmount" id="asfAmount" class="form-control" required>
                </div>
                <div class="col-md-12">
                  <label for="asfYear">Paid For Year</label>
                  <select name="asfYear" id="asfYear" required class="form-control">
                    <option value="2020">2020</option>
                    <option value="2021">2021</option>
                    <option value="2022">2022</option>
                    <option value="2023">2023</option>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                  </select>
                </div>
                <div class="col-md-12">
                  <label for="asfRemarks">Remarks</label>
                  <textarea class="form-control" name="asfRemarks" id="asfRemarks" cols="30" rows="10" required></textarea>
                </div>
                <div class="col-md-12">
                  <label for="asfWaveOff">Wave Off</label>
                  <input type="checkbox" name="asfWaveOff" id="asfWaveOff">
                </div>

              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Add</button>
            </div>
          </form>
        </div>
      </div>
    </div>


    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Summary</h4>
          </div>
          <div class="card-content">
            <div class="card-body card-dashboard">
              FCLP Cost: {{ $client->latestPackage->productCost }} <br>
              Downpayment: {{ $client->downPayment }} <br>
              Total Payment Done(Including DP): {{ $totalTransactions->pluck('amount')->sum() }} <br>
              Pending Payment: {{ $client->latestPackage->productCost - $totalTransactions->pluck('amount')->sum()  }}
              <br>
              @if($user->employee)
                @if($client->refundRequest)
                  @if($client->refundRequest->approval_accounts_by)
                    Refunded Amount: {{ $client->refundRequest->approval_accounts_amount }}
                  @endif
                @endif
                <br>
                <hr>
                <strong>
                  @if($client->refundRequest)
                    Refund Request Added On: {{ $client->refundRequest->refund_date }}
                    @if($client->refundRequest->approved_rejected == 2)
                      (Denied By Manager | Remarks: {{ $client->refundRequest->approved_rejected_remarks }})
                    @elseif($client->refundRequest->accepted_denied == 2)
                      (Denied By MRD | Remarks: {{ $client->refundRequest->accepted_denied_remarks }})
                    @elseif($client->refundRequest->approval_accounts_by)
                      (Approved By Accounts | Amount: {{ $client->refundRequest->approval_accounts_amount }} |
                      Remarks: {{ $client->refundRequest->approval_accounts_remarks }})
                    @endif
                    {{--        {{ $client->refundRequest }}--}}
                  @endif

                  @endif
                </strong>
            </div>
          </div>
        </div>

      </div>


      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Total Payments</h4>
          </div>
          <div class="card-content">
            <div class="card-body card-dashboard">
              <div class="table-responsive">
                <table class="table zero-configuration">
                  <thead>
                  <tr>
                    <th>Payment Date</th>
                    <th>Amount</th>
                    <th>Mode Of payment</th>
                    <th>Remarks</th>
                    <th>DP</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($totalTransactions as $transaction)
                    <tr>
                      <td>{{ $transaction['date'] }}</td>
                      <td>{{ $transaction['amount'] }}</td>
                      <td>{{ $transaction['mode'] }}</td>
                      <td>{{ $transaction['remarks'] }}</td>
                      <td>{{ $transaction['dp'] == 1 ? 'Downpayment' : 'EMI' }}</td>
                    </tr>
                  @endforeach
                  </tbody>

                  <tfoot>
                  <tr>
                    <th>Payment Date</th>
                    <th>Amount</th>
                    <th>Mode Of payment</th>
                    <th>Remarks</th>
                    <th>DP</th>
                  </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>

      </div>

      @if($client->cardPayments->count())
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Card Payments</h4>
            </div>
            <div class="card-content">
              <div class="card-body card-dashboard">
                <div class="table-responsive">
                  <table class="table zero-configuration">
                    <thead>
                    <tr>
                      <th>Payment Date</th>
                      <th>Amount</th>
                      <th>Card Type</th>
                      <th>Last Four Digits</th>
                      <th>Remarks</th>
                      <th>DP</th>
                      @if($user->employee)
                        <th>Action</th>
                      @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($client->CardPayments as $cardPayment)
                      <tr>
                        <td>{{ $cardPayment->paymentDate }}</td>
                        <td>{{ $cardPayment->amount }}</td>
                        <td>{{ $cardPayment->cardType }}</td>
                        <td>{{ $cardPayment->cardLastFourDigits }}</td>
                        <td>{{ $cardPayment->remarks }}</td>
                        <td>{{ $cardPayment->isDp == 1 ? 'Downpayment' : 'EMI' }}</td>
                        @if($user->employee)

                          <td>
                            <button class="btn btn-primary btn-sm" data-toggle="modal"
                                    data-target="#editCardPayment{{$cardPayment->id}}">Edit
                            </button>
                            <div class="modal fade" id="editCardPayment{{$cardPayment->id}}" tabindex="-1" role="dialog"
                                 aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Edit Card Transaction</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <form
                                    action="{{ route('edit.transaction.card',['transactionId'=>$cardPayment->id]) }}"
                                    method="POST">
                                    @csrf
                                    <div class="modal-body">
                                      <div class="row">
                                        <div class="col-md-12">
                                          <label for="paymentDate">Payment Date</label>
                                          <input type="date" class="form-control" name="paymentDate"
                                                 value="{{ $cardPayment->paymentDate }}" required>
                                        </div>
                                        <div class="col-md-12">
                                          <label for="paymentAmount">Amount</label>
                                          <input type="number" class="form-control" name="paymentAmount"
                                                 value="{{ $cardPayment->amount }}" required>
                                        </div>
                                        <div class="col-md-12">
                                          <label for="paymentCardType">Card Type</label>
                                          <input type="text" class="form-control" name="paymentCardType"
                                                 value="{{ $cardPayment->cardType }}" required>
                                        </div>
                                        <div class="col-md-12">
                                          <label for="paymentRemarks">Remarks</label>
                                          <input type="text" class="form-control" name="paymentRemarks"
                                                 value="{{ $cardPayment->remarks }}" required>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close
                                      </button>
                                      <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                  </form>
                                </div>
                              </div>
                            </div>

                          </td>
                        @endif
                      </tr>
                    @endforeach
                    </tbody>

                    <tfoot>
                    <tr>
                      <th>Payment Date</th>
                      <th>Amount</th>
                      <th>Card Type</th>
                      <th>Last Four Digits</th>
                      <th>Remarks</th>
                      <th>DP</th>
                      @if($user->employee)

                        <th>Action</th>
                      @endif
                    </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>

        </div>
      @endif


      @if($client->cashPayments->count())
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Cash Payments</h4>
            </div>
            <div class="card-content">
              <div class="card-body card-dashboard">
                <div class="table-responsive">
                  <table class="table zero-configuration">
                    <thead>
                    <tr>
                      <th>Payment Date</th>
                      <th>Amount</th>
                      <th>Receipt Number</th>
                      <th>Remarks</th>
                      <th>DP</th>
                      @if($user->employee)

                        <th>Action</th>
                      @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($client->cashPayments as $cashPayment)
                      <tr>
                        <td>{{ $cashPayment->paymentDate }}</td>
                        <td>{{ $cashPayment->amount }}</td>
                        <td>{{ $cashPayment->receiptNumber }}</td>
                        <td>{{ $cashPayment->remarks }}</td>
                        @if($user->employee)

                          <td>{{ $cashPayment->isDp ? 'Downpayment' : 'EMI'}}</td>
                        @endif
                        <td>
                          <button class="btn btn-primary btn-sm" data-toggle="modal"
                                  data-target="#editCashPayment{{$cashPayment->id}}">Edit
                          </button>
                          <div class="modal fade" id="editCashPayment{{$cashPayment->id}}" tabindex="-1" role="dialog"
                               aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLongTitle">Edit Cash Transaction</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <form action="{{ route('edit.transaction.cash',['transactionId'=>$cashPayment->id]) }}"
                                      method="POST">
                                  @csrf
                                  <div class="modal-body">
                                    <div class="row">
                                      <div class="col-md-12">
                                        <label for="paymentDate">Payment Date</label>
                                        <input type="date" class="form-control" name="paymentDate"
                                               value="{{ $cashPayment->paymentDate }}" required>
                                      </div>
                                      <div class="col-md-12">
                                        <label for="paymentAmount">Amount</label>
                                        <input type="number" class="form-control" name="paymentAmount"
                                               value="{{ $cashPayment->amount }}" required>
                                      </div>
                                      <div class="col-md-12">
                                        <label for="paymentReceiptNumber">Receipt Number</label>
                                        <input type="text" class="form-control" name="paymentReceiptNumber"
                                               value="{{ $cashPayment->receiptNumber }}" required>
                                      </div>
                                      <div class="col-md-12">
                                        <label for="paymentRemarks">Remarks</label>
                                        <input type="text" class="form-control" name="paymentRemarks"
                                               value="{{ $cashPayment->remarks }}" required>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                  </div>
                                </form>
                              </div>
                            </div>
                          </div>

                        </td>

                      </tr>
                    @endforeach
                    </tbody>

                    <tfoot>
                    <tr>
                      <th>Payment Date</th>
                      <th>Amount</th>
                      <th>Receipt Number</th>
                      <th>Remarks</th>
                      <th>DP</th>
                      @if($user->employee)

                        <th>Action</th>
                      @endif
                    </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>

        </div>
      @endif


      @if($client->chequePayments->count())
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Cheque Payments</h4>
            </div>
            <div class="card-content">
              <div class="card-body card-dashboard">
                <div class="table-responsive">
                  <table class="table zero-configuration">
                    <thead>
                    <tr>
                      <th>Payment Date</th>
                      <th>Amount</th>
                      <th>Cheque Number</th>
                      <th>Cheque Issuer</th>
                      <th>Cheque Clearing Bank</th>
                      <th>Remarks</th>
                      <th>DP</th>
                      @if($user->employee)

                        <th>Action</th>
                      @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($client->ChequePayments as $chequePayment)
                      <tr>
                        <td>{{ $chequePayment->paymentDate }}</td>
                        <td>{{ $chequePayment->amount }}</td>
                        <td>{{ $chequePayment->chequeNumber }}</td>
                        <td>{{ $chequePayment->chequeIssuer }}</td>
                        <td>{{ $chequePayment->chequeClearingBank }}</td>
                        <td>{{ $chequePayment->remarks }}</td>
                        <td>{{ $chequePayment->isDp == 1 ? 'Downpayment' : 'EMI' }}</td>
                        @if($user->employee)

                          <td>
                            <button class="btn btn-primary btn-sm" data-toggle="modal"
                                    data-target="#editChequePayment{{$chequePayment->id}}">Edit
                            </button>
                            <div class="modal fade" id="editChequePayment{{$chequePayment->id}}" tabindex="-1"
                                 role="dialog" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Edit Cheque Transaction</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <form
                                    action="{{ route('edit.transaction.cheque',['transactionId'=>$chequePayment->id]) }}"
                                    method="POST">
                                    @csrf
                                    <div class="modal-body">
                                      <div class="row">
                                        <div class="col-md-12">
                                          <label for="paymentDate">Payment Date</label>
                                          <input type="date" class="form-control" name="paymentDate"
                                                 value="{{ $chequePayment->paymentDate }}" required>
                                        </div>
                                        <div class="col-md-12">
                                          <label for="paymentAmount">Amount</label>
                                          <input type="number" class="form-control" name="paymentAmount"
                                                 value="{{ $chequePayment->amount }}" required>
                                        </div>
                                        <div class="col-md-12">
                                          <label for="paymentChequeNumber">Cheque Number</label>
                                          <input type="text" class="form-control" name="paymentChequeNumber"
                                                 value="{{ $chequePayment->chequeNumber }}" required>
                                        </div>
                                        <div class="col-md-12">
                                          <label for="paymentChequeIssuer">Cheque Issuer</label>
                                          <input type="text" class="form-control" name="paymentChequeIssuer"
                                                 value="{{ $chequePayment->chequeIssuer }}" required>
                                        </div>
                                        <div class="col-md-12">
                                          <label for="paymentChequeClearingBank">Cheque Clearing Bank</label>
                                          <input type="text" class="form-control" name="paymentChequeClearingBank"
                                                 value="{{ $chequePayment->chequeClearingBank }}" required>
                                        </div>
                                        <div class="col-md-12">
                                          <label for="paymentRemarks">Remarks</label>
                                          <input type="text" class="form-control" name="paymentRemarks"
                                                 value="{{ $chequePayment->remarks }}" required>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close
                                      </button>
                                      <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                  </form>
                                </div>
                              </div>
                            </div>

                          </td>

                        @endif

                      </tr>
                    @endforeach
                    </tbody>

                    <tfoot>
                    <tr>
                      <th>Payment Date</th>
                      <th>Amount</th>
                      <th>Cheque Number</th>
                      <th>Cheque Issuer</th>
                      <th>Cheque Clearing Bank</th>
                      <th>Remarks</th>
                      <th>DP</th>
                      @if($user->employee)

                        <th>Action</th>
                      @endif
                    </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>

        </div>
      @endif

      @if($client->otherPayments->count())
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Other Payments</h4>
            </div>
            <div class="card-content">
              <div class="card-body card-dashboard">
                <div class="table-responsive">
                  <table class="table zero-configuration">
                    <thead>
                    <tr>
                      <th>Payment Date</th>
                      <th>Amount</th>
                      <th>Mode Of Payment</th>
                      <th>Remarks</th>
                      <th>DP</th>
                      @if($user->employee)

                        <th>Action</th>
                      @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($client->otherPayments as $otherPayment)
                      <tr>
                        <td>{{ $otherPayment->paymentDate }}</td>
                        <td>{{ $otherPayment->amount }} <span class="danger">{{ $otherPayment->paymentBreatherCharges ? '(Breather Charge)':'' }}</span></td>
                        <td>{{ $otherPayment->modeOfPayment }}</td>
                        <td>{{ $otherPayment->remarks }}</td>
                        <td>{{ $otherPayment->idDp }}</td>
                        @if($user->employee)

                          <td>
                            <button class="btn btn-primary btn-sm" data-toggle="modal"
                                    data-target="#editOtherPayment{{$otherPayment->id}}">Edit
                            </button>
                            <div class="modal fade" id="editOtherPayment{{$otherPayment->id}}" tabindex="-1"
                                 role="dialog" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Edit Cheque Transaction</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <form
                                    action="{{ route('edit.transaction.others',['transactionId'=>$otherPayment->id]) }}"
                                    method="POST">
                                    @csrf
                                    <div class="modal-body">
                                      <div class="row">
                                        <div class="col-md-12">
                                          <label for="paymentDate">Payment Date</label>
                                          <input type="date" class="form-control" name="paymentDate"
                                                 value="{{ $otherPayment->paymentDate }}" required>
                                        </div>
                                        <div class="col-md-12">
                                          <label for="paymentAmount">Amount</label>
                                          <input type="number" class="form-control" name="paymentAmount"
                                                 value="{{ $otherPayment->amount }}" required>
                                        </div>
                                        <div class="col-md-12">
                                          <label for="paymentChequeNumber">Mode Of Payment</label>
                                          <input type="text" class="form-control" name="modeOfPayment"
                                                 value="{{ $otherPayment->modeOfPayment }}" required>
                                        </div>

                                        <div class="col-md-12">
                                          <label for="paymentRemarks">Remarks</label>
                                          <input type="text" class="form-control" name="paymentRemarks"
                                                 value="{{ $otherPayment->remarks }}" required>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close
                                      </button>
                                      <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                  </form>
                                </div>
                              </div>
                            </div>

                          </td>

                        @endif

                      </tr>
                    @endforeach
                    </tbody>

                    <tfoot>
                    <tr>
                      <th>Payment Date</th>
                      <th>Amount</th>
                      <th>Mode Of Payment</th>
                      <th>Remarks</th>
                      <th>DP</th>
                      @if($user->employee)
                        <th>Action</th>
                      @endif
                    </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>

        </div>
      @endif


      @if($client->AxisPayments->count() and $client->id != 590)
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Axis NACH Payments(UMRN: {{  $client->AxisPayments->first()->umrn }} |
                Success: {{ $client->axisPayments->where('status_description','Success')->pluck('amount')->sum()   }} |
                Failed: {{ $client->AxisPayments->where('status_description','INITIAL REJECTION')->pluck('amount')->sum()   }}
                |
                From {{ \Carbon\Carbon::parse($client->axisMis->last()->STARTDATE)->format('d-m-Y') }} To
                {{ \Carbon\Carbon::parse($client->axisMis->last()->ENDDATE)->format('d-m-Y') }}
                )
              </h4>
            </div>
            <div class="card-content">
              <div class="card-body card-dashboard">
                <div class="table-responsive">
                  <table class="table zero-configuration">
                    <thead>
                    <tr>
                      <th>Payment Date</th>
                      <th>Amount</th>
                      <th>Status</th>
                      <th>Reason</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($client->axisPayments as $axisPayment)
                      <tr>
                        <td>{{ $axisPayment->date_of_transaction }} {{ $axisPayment->client_id }}</td>
                        <td>{{ $axisPayment->amount }}</td>
                        <td>{{ $axisPayment->status_description }}</td>
                        <td>{{ $axisPayment->reason_description }}</td>
                      </tr>
                    @endforeach
                    @if($client->id == 786)
                      @foreach(\App\Client\Transaction\AxisNachPayment::where('client_id',590)->get() as $axisPaymentt)
                        <tr>
                          <td>{{ $axisPaymentt->date_of_transaction }}</td>
                          <td>{{ $axisPaymentt->amount }}</td>
                          <td>{{ $axisPaymentt->status_description }}</td>
                          <td>{{ $axisPaymentt->reason_description }}</td>
                        </tr>
                      @endforeach
                    @endif


                    </tbody>

                    <tfoot>
                    <tr>
                      <th>Payment Date</th>
                      <th>Amount</th>
                      <th>Status</th>
                      <th>Reason</th>
                    </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>

        </div>
      @endif


      @if($client->YesPayments->count())
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Yes NACH Payments(Reference: {{  $client->YesPayments->first()->ITEM_REFERENCE }} |
                Success: {{ $client->YesPayments->where('STATUS','ACCEPTED')->pluck('AMOUNT')->sum()   }} |
                Failed: {{ $client->YesPayments->where('STATUS','RETURNED')->pluck('AMOUNT')->sum()   }})</h4>
            </div>
            <div class="card-content">
              <div class="card-body card-dashboard">
                <div class="table-responsive">
                  <table class="table zero-configuration">
                    <thead>
                    <tr>
                      <th>Payment Date</th>
                      <th>Amount</th>
                      <th>Status</th>
                      <th>Reason</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($client->yesPayments as $yesPayment)
                      <tr>
                        <td>{{ $yesPayment->VALUE_DATE }}</td>
                        <td>{{ $yesPayment->AMOUNT }}</td>
                        <td>{{ $yesPayment->STATUS }}</td>
                        <td>{{ $yesPayment->REASON_CODE }}</td>
                      </tr>
                    @endforeach
                    </tbody>

                    <tfoot>
                    <tr>
                      <th>Payment Date</th>
                      <th>Amount</th>
                      <th>Status</th>
                      <th>Reason</th>

                    </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>

        </div>
      @endif

      {{--    @foreach($client->TimelineActivity->sortByDesc('created_at') as $activity)--}}
      {{--      <div class="col-md-12">--}}
      {{--        <div class="card">--}}
      {{--          <div class="card-body">--}}
      {{--            <div class="d-flex justify-content-start align-items-center mb-1">--}}
      {{--              <div class="avatar mr-1">--}}
      {{--                <img src="{{ avatar($activity->User->name) }}" alt="avtar img holder" height="45"--}}
      {{--                     width="45">--}}
      {{--              </div>--}}
      {{--              <div class="user-page-info">--}}
      {{--                <h6 class="mb-0">{{ $activity->User->name }}</h6>--}}
      {{--                <span class="font-small-2">{{readableDate($activity->created_at) }}</span>--}}
      {{--              </div>--}}
      {{--            </div>--}}
      {{--            <p>{{ $activity->title }}</p>--}}
      {{--            {!! $activity->body !!}--}}
      {{--            <div class="d-flex justify-content-start align-items-center mb-1">--}}

      {{--              <p class="ml-auto d-flex align-items-center">--}}
      {{--                <i class="feather icon-message-square font-medium-2 mr-50"></i>{{ $activity->comments->count() }}--}}
      {{--              </p>--}}
      {{--            </div>--}}
      {{--            @forelse($activity->comments as $comment)--}}
      {{--              <div class="d-flex justify-content-start align-items-center mb-1">--}}
      {{--                <div class="avatar mr-50">--}}
      {{--                  <img src="{{ avatar($comment->User->name) }}" alt="Avatar" height="30" width="30">--}}
      {{--                </div>--}}
      {{--                <div class="user-page-info">--}}
      {{--                  <h6 class="mb-0">{{ $comment->User->name }} @ <span class="font-small-2">{{ readableDate($comment->created_at) }}</span></h6>--}}
      {{--                  <span class="font-small-4">{{ $comment->body }}</span>--}}
      {{--                  <span class="font-small-1"></span>--}}
      {{--                </div>--}}
      {{--              </div>--}}
      {{--            @empty--}}
      {{--              No Comments--}}
      {{--            @endforelse--}}
      {{--            <form action="{{ route('create.client.timelineComment',['activityId'=>$activity->id]) }}" method="POST">--}}
      {{--              @csrf--}}
      {{--              <fieldset class="form-label-group mb-50">--}}
      {{--                <textarea class="form-control" id="label-textarea3" rows="3" placeholder="Add Comment" name="activityComment"></textarea>--}}
      {{--                <label for="label-textarea3">Add Comment</label>--}}
      {{--              </fieldset>--}}
      {{--              <button type="submit" class="btn btn-sm btn-primary">Save Comment</button>--}}
      {{--            </form>--}}
      {{--          </div>--}}
      {{--        </div>--}}
      {{--      </div>--}}
      {{--    @endforeach--}}

    </div>
  @elseif(request()->type == 'addOn')
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Add On Payments(Total: {{ $client->addOnPayment }})</h4>
          </div>
          <div class="card-content">
            <div class="card-body card-dashboard">
              <div class="table-responsive">
                <table class="table zero-configuration">
                  <thead>
                  <tr>
                    <th>Payment Date</th>
                    <th>Amount</th>
                    <th>Mode Of payment</th>
                    <th>Remarks</th>
                    <th>DP</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($addOnTransactions as $transaction)
                    <tr>
                      <td>{{ $transaction['date'] }}</td>
                      <td>{{ $transaction['amount'] }}</td>
                      <td>{{ $transaction['mode'] }}</td>
                      <td>{{ $transaction['remarks'] }}</td>
                      <td>{{ $transaction['dp'] == 1 ? 'Downpayment' : 'EMI' }}</td>
                    </tr>
                  @endforeach
                  </tbody>

                  <tfoot>
                  <tr>
                    <th>Payment Date</th>
                    <th>Amount</th>
                    <th>Mode Of payment</th>
                    <th>Remarks</th>
                    <th>DP</th>
                  </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>


      </div>
    </div>

  @endif
</div>

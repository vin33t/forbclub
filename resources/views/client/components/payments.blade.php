<div class="col-lg-12 col-12">
  @php
    $totalTransactions = collect();
      if($client->cashPayments->count()){
          foreach($client->CashPayments as $ca){
            $totalTransactions->push(['date'=>$ca->paymentDate,'amount'=>$ca->amount,'remarks'=>$ca->remarks,'mode'=>'Cash','dp'=>$ca->isDp]);
          }
      }
      if($client->cardPayments->count()){
          foreach($client->CardPayments as $cad){
            $totalTransactions->push(['date'=>$cad->paymentDate,'amount'=>$cad->amount,'remarks'=>$cad->remarks,'mode'=>'Card','dp'=>$cad->isDp]);
          }
      }
      if($client->chequePayments->count()){
          foreach($client->chequePayments as $che){
            $totalTransactions->push(['date'=>$che->paymentDate,'amount'=>$che->amount,'remarks'=>$che->remarks,'mode'=>'Cheque','dp'=>$che->isDp]);
          }
      }
      if($client->otherPayments->count()){
          foreach($client->otherPayments as $oth){
            $totalTransactions->push(['date'=>$oth->paymentDate,'amount'=>$oth->amount,'remarks'=>$oth->remarks,'mode'=>$oth->modeOfPayment,'dp'=>$oth->isDp]);
          }
      }

      if($client->AxisPayments->count()){
          foreach($client->AxisPayments as $axp){
            if($axp->status_description == 'success' or $axp->status_description == 'SUCCESS' or $axp->status_description == 'Success'){
              $totalTransactions->push(['date'=>$axp->date_of_transaction,'amount'=>$axp->amount,'remarks'=>$axp->reason_description,'mode'=>'AXIS NACH','dp'=>'']);
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
  @endphp
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
                    @foreach($totalTransactions as $transaciton)
                      <tr>
                        <td>{{ $transaciton['date'] }}</td>
                        <td>{{ $transaciton['amount'] }}</td>
                        <td>{{ $transaciton['mode'] }}</td>
                        <td>{{ $transaciton['remarks'] }}</td>
                        <td>{{ $transaciton['dp'] == 1 ? 'Downpayment' : 'EMI' }}</td>
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

                  <td><button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editCardPayment{{$cardPayment->id}}">Edit</button>
                    <div class="modal fade" id="editCardPayment{{$cardPayment->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Edit Card Transaction</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <form action="{{ route('edit.transaction.card',['transactionId'=>$cardPayment->id]) }}" method="POST">
                            @csrf
                          <div class="modal-body">
                            <div class="row">
                              <div class="col-md-12">
                                <label for="paymentDate">Payment Date</label>
                                <input type="date" class="form-control" name="paymentDate" value="{{ $cardPayment->paymentDate }}" required>
                              </div>
                              <div class="col-md-12">
                                <label for="paymentAmount">Amount</label>
                                <input type="number" class="form-control" name="paymentAmount" value="{{ $cardPayment->amount }}" required>
                              </div>
                              <div class="col-md-12">
                                <label for="paymentCardType">Card Type</label>
                                <input type="text" class="form-control" name="paymentCardType" value="{{ $cardPayment->cardType }}" required>
                              </div>
                              <div class="col-md-12">
                                <label for="paymentRemarks">Remarks</label>
                                <input type="text" class="form-control" name="paymentRemarks" value="{{ $cardPayment->remarks }}" required>
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
                    @endif
                </tr>
                 @endforeach
                </tbody>

                <tfoot>
                <tr>make.partialPayment
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
                  <td><button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editCashPayment{{$cashPayment->id}}">Edit</button>
                    <div class="modal fade" id="editCashPayment{{$cashPayment->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Edit Cash Transaction</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <form action="{{ route('edit.transaction.cash',['transactionId'=>$cashPayment->id]) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                              <div class="row">
                                <div class="col-md-12">
                                  <label for="paymentDate">Payment Date</label>
                                  <input type="date" class="form-control" name="paymentDate" value="{{ $cashPayment->paymentDate }}" required>
                                </div>
                                <div class="col-md-12">
                                  <label for="paymentAmount">Amount</label>
                                  <input type="number" class="form-control" name="paymentAmount" value="{{ $cashPayment->amount }}" required>
                                </div>
                                <div class="col-md-12">
                                  <label for="paymentReceiptNumber">Receipt Number</label>
                                  <input type="text" class="form-control" name="paymentReceiptNumber" value="{{ $cashPayment->receiptNumber }}" required>
                                </div>
                                <div class="col-md-12">
                                  <label for="paymentRemarks">Remarks</label>
                                  <input type="text" class="form-control" name="paymentRemarks" value="{{ $cashPayment->remarks }}" required>
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

                        <td><button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editChequePayment{{$chequePayment->id}}">Edit</button>
                          <div class="modal fade" id="editChequePayment{{$chequePayment->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLongTitle">Edit Cheque Transaction</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <form action="{{ route('edit.transaction.cheque',['transactionId'=>$chequePayment->id]) }}" method="POST">
                                  @csrf
                                  <div class="modal-body">
                                    <div class="row">
                                      <div class="col-md-12">
                                        <label for="paymentDate">Payment Date</label>
                                        <input type="date" class="form-control" name="paymentDate" value="{{ $chequePayment->paymentDate }}" required>
                                      </div>
                                      <div class="col-md-12">
                                        <label for="paymentAmount">Amount</label>
                                        <input type="number" class="form-control" name="paymentAmount" value="{{ $chequePayment->amount }}" required>
                                      </div>
                                      <div class="col-md-12">
                                        <label for="paymentChequeNumber">Cheque Number</label>
                                        <input type="text" class="form-control" name="paymentChequeNumber" value="{{ $chequePayment->chequeNumber }}" required>
                                      </div>
                                      <div class="col-md-12">
                                        <label for="paymentChequeIssuer">Cheque Issuer</label>
                                        <input type="text" class="form-control" name="paymentChequeIssuer" value="{{ $chequePayment->chequeIssuer }}" required>
                                      </div>
                                      <div class="col-md-12">
                                        <label for="paymentChequeClearingBank">Cheque Clearing Bank</label>
                                        <input type="text" class="form-control" name="paymentChequeClearingBank" value="{{ $chequePayment->chequeClearingBank }}" required>
                                      </div>
                                      <div class="col-md-12">
                                        <label for="paymentRemarks">Remarks</label>
                                        <input type="text" class="form-control" name="paymentRemarks" value="{{ $chequePayment->remarks }}" required>
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
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($client->otherPayments as $otherPayment)
                      <tr>
                        <td>{{ $otherPayment->paymentDate }}</td>
                        <td>{{ $otherPayment->amount }}</td>
                        <td>{{ $otherPayment->modeOfPayment }}</td>
                        <td>{{ $otherPayment->remarks }}</td>
                        <td>{{ $otherPayment->idDp }}</td>
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
                Failed: {{ $client->AxisPayments->where('status_description','INITIAL REJECTION')->pluck('amount')->sum()   }} |
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
                Success: {{ $client->YesPayments->where('STATUS','ACCEPTED')->pluck('AMOUNT')->sum()   }} | Failed: {{ $client->YesPayments->where('STATUS','RETURNED')->pluck('AMOUNT')->sum()   }})</h4>
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
</div>

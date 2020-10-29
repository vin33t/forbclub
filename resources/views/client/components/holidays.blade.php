<div class="col-lg-12 col-md-12">
  @if($client->ClientHoliday->count())
    <div class="row">
      <div class="col-md-12">
        <div class="card card-box">
          <div class="card-head">
            <div class="col-sm-12 center">
              <h4 class="font-weight-bold">
                <b>New Holiday Packages</b>
              </h4>
            </div>
          </div>
        </div>
      </div>
{{--      {{ dd($client->ClientHoliday) }}--}}
      @foreach($client->ClientHoliday as $ch)
        <div class="col-md-4">
          <div class="card card-box" @if($ch->cancelled) style="background-color: #FCC8C8" @endif>
            <div class="card-head text-center">
              <div class="col-md-12 center"><h4 class="font-weight-bold"><b>{{ $ch->destination }}</b></h4></div>
{{--              <div class="col-md-3 pull-right">--}}
{{--                @if(!$ch->cancelled)--}}
{{--                  <span class="dropdown">--}}
{{--                                        <button class="btn btn-danger" onclick="sendThis({{ $ch->id }})" data-toggle="modal" data-target="#cancelHoliday">--}}
{{--                                                    <i class="fa fa-trash-o" style="color:white"></i>--}}
{{--                                                </button>--}}
{{--                                    </span>--}}
{{--                @endif--}}
{{--              </div>--}}
            </div>
            @if($ch->cancelled)
              <div class="text-center">
                Holiday Cancelled by <strong>{{ App\User::find($ch->cancelled_by)->name }}</strong> on <strong>{{ Carbon\Carbon::parse($ch->cancelled_on)->format('F d, Y h:i A') }}</strong>
              </div>
            @endif
            <div class="modal fade" id="cancelHoliday" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <form action="#" method="post">
                    @csrf
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Cancel Holiday</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <input type="hidden" name="id" id="myId">
                      <textarea class="form-control" name="remarks" placeholder="Cancel Remarks"></textarea>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-danger">Cancel Holiday</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <script>
              function sendThis(id){
                $('#myId').val(id);
              }
            </script>
            <div class="card-body text-center">
              <div class="row">
                <div class="col-md-6">Travel Date <br> <b>{{ Carbon\Carbon::parse($ch->date_of_travel)->format('M d,Y') }} <br>
                    {{ Carbon\Carbon::parse($ch->date_of_travel)->format('l') }}
                  </b></div>
                <div class="col-md-6">Holiday Type <br> <b>{{ $ch->holiday_type }}</b></div>
              </div>
              @php
                $b = \App\Client\Booking\Bookings::find($ch->bookings_id);
              @endphp
              @php
                $bookingAmount = 0;
                foreach($b->BookingOffer->BookingOfferInfo as $offer){
                    if($offer->add_on == 0){
                        $bookingAmount += $offer->our_price;
                    } elseif($offer->add_on == 1){
                        $bookingAmount += $offer->add_on_service_price;
                    }
                }
             $holidayAmount = 0;
                            $holidayAmountPaidByClient = 0;
                            foreach($b->ClientHoliday->ClientHolidayDetails as $details){
                                foreach ($details->ClientHolidayTransactions as $transaction){
                                    $holidayAmount += $transaction->amount;
                                        $holidayAmountPaidByClient += (int)$details->amount_paid_by_client;
                                }
                            }
              @endphp
              <hr>
              <div class="row">
                <div class="col-md-3">Booking Amount <br><strong> {{ IND_money_format($bookingAmount) }}</strong></div>
                <div class="col-md-3">Holiday Amount<br><strong>{{ IND_money_format($holidayAmount) }}</strong></div>
              </div>
              <hr>
              <u><strong>Booking Request Details</strong></u>
              <div class="text-center">
                <strong>Converted By:</strong> {{App\User::find($b->ClientHoliday->converted_by)->name}} <br>
                <strong>Converted On:</strong> {{ \Carbon\Carbon::parse($b->ClientHoliday->created_at)->format('l, F jS, Y\\ h:i A') }}
                <hr>
                <strong>Verified By:</strong> {{ App\User::find($b->statusUpdatedBy)->name }} <br>
                <strong>Verification On:</strong> @if($b->approved_rejected_on) {{ \Carbon\Carbon::parse($b->statusUpdatedOn)->format('l, F jS, Y\\ h:i A') }} @endif <br>
                <strong>Verification Remarks:</strong> {{$b->statusRemarks}}
                <hr>
                <strong>Offer Approved By:</strong>

                  {{App\User::find($b->offerStatusUpdatedBy)->name}}
                <br>
                <strong>Approved On:</strong> {{ \Carbon\Carbon::parse($b->offerStatusUpdatedBy)->format('l, F jS, Y\\ h:i A') }} <br>
                <strong>Approval Remarks:</strong> {{$b->offerStatusRemarks}}
              </div>
              <hr>
              @foreach($ch->ClientHolidayDetails as $chd)
                <h4 class="font-weight-bold">
                  <a href="javascript:void(0);" onclick="chaljaPlease(this);">
                    {{ $chd->service_type }} @if($chd->add_on)(Add On Service)@endif
                    <input type="hidden" class="chd" value="{{$chd}}">
                    <input type="hidden" class="pcht" value="{{$chd->ClientHolidayTransactions->where('paid',1)}}">
                    <input type="hidden" class="ucht" value="{{$chd->ClientHolidayTransactions->where('paid',0)->where('amount','!=',0)}}">
                  </a>
                  <a href="javascript:void(0);" onclick="partialPayment('{{$chd->id}}','{{$chd->add_on}}','{{$ch->client->id}}')"><span class="fa fa-plus"></span></a>
                  @if($chd->service_type == 'Hotel' or $chd->service_type == 'Land Package/Transfer') <br>
                  {{ $chd->hotel_name }} ({{ $chd->destination }})<br>
                  {{ $chd->nights }} Nights  {{ '(' . $chd->vendor_name . ')'}} <br>
                  ({{ \Carbon\Carbon::parse($chd->check_in)->format('l, F jS, Y\\') }} - {{ \Carbon\Carbon::parse($chd->check_out)->format('l, F jS, Y\\') }})
                  @endif
                  @if($chd->service_type == 'Flight') <br>
                  {{ '(' . $chd->vendor_name . ')'}} <br>
                  ({{ $chd->flight_details }}) {{ $chd->flight_pax }}
                  @endif
                </h4>
                @if($chd->ClientHolidayTransactions->where('amount','!=',0)->count())
                  <div class="card box">
                    <div class="card-body">
                      <div class="text-center">
                        <strong><u>Transactions</u></strong>
                      </div>
                      <div class="row">
                        <div class="col-md-4">
                          <strong>Amount</strong>
                        </div>
                        <div class="col-md-4">
                          <strong>Date</strong>
                        </div>
                        <div class="col-md-4">
                          <strong>Status</strong>
                        </div>
                      </div>
                      @foreach($chd->ClientHolidayTransactions->where('amount','!=',0) as $cht)
                        <div class="row">
                          <div class="col-md-4">
                            ₹{{$cht->amount}}
                          </div>
                          <div class="col-md-4">
                            {{Carbon\Carbon::parse($cht->date_of_payment)->format('M d,Y')}} <br>
                            {{Carbon\Carbon::parse($cht->date_of_payment)->format('l')}}
                          </div>
                          <div class="col-md-4">
                            @if($cht->paid)
                              <span class="text-success"><strong>{{__('Paid')}}</strong></span>
                            @else
                              <span class="text-danger"><strong>{{__('UnPaid')}}</strong> <button class="btn btn-primary btn-sm" onclick="makePayment('{{ $cht->id }}','{{ $cht->amount }}','{{ $cht->date_of_payment }}')">Pay Now</button> </span>
                            @endif
                          </div>
                        </div><br>
                      @endforeach
                    </div>
                  </div>
                @endif
                <hr>
              @endforeach
              <div class="row">
                <div class="col-md-4">
                  <strong><strong>Total Amount</strong></strong>
                </div>
                <div class="col-md-4">
                  <strong><strong>Paid</strong></strong>
                </div>
                <div class="col-md-4">
                  <strong><strong>Unpaid</strong></strong>
                </div>
              </div>
              @php
                $total_amount = 0;
                $total_paid = 0;
                $total_unpaid = 0;
                $total_amount_add_on = 0;
                $total_amount_fclp = 0;
                foreach(\App\Client\Holiday\ClientHolidayDetails::where('client_holiday_id',$ch->id)->get() as $chd){
                    $total_amount = $total_amount + $chd->ClientHolidayTransactions->pluck('amount')->sum();
                    $total_amount_add_on += $chd->ClientHolidayTransactions->where('add_on',1)->pluck('amount')->sum();
                    $total_amount_fclp += $chd->ClientHolidayTransactions->where('add_on',0)->pluck('amount')->sum();
                    $total_paid = $total_paid + $chd->ClientHolidayTransactions->where('paid',1)->pluck('amount')->sum();
                    $total_unpaid = $total_unpaid + $chd->ClientHolidayTransactions->where('paid',0)->pluck('amount')->sum();
                }
              @endphp
              <div class="row">
                <div class="col-md-4">
                  <span class="text-info">₹{{$total_amount}}</span>
                </div>
                <div class="col-md-4">
                  <span class="text-success">₹{{$total_paid}}</span>
                </div>
                <div class="col-md-4">
                  <span class="text-danger">₹{{$total_unpaid}}</span>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6"><strong>Against FCLP:</strong><span class="text-info">₹{{ IND_money_format($total_amount_fclp) }}</span></div>
                <div class="col-md-6"><strong>Addon: </strong><span class="text-danger">₹{{ IND_money_format($total_amount_add_on) }}</span></div>
              </div>
            </div>

          </div>
        </div>
      @endforeach
    </div>
  @endif

</div>
<div class="modal fade" id="partialPaymentModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Partial Payment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="" method="post">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="holidayDetailsId" id="holidayDetailsId">
          <input type="hidden" name="addonPartial" id="addonPartial">
          <input type="hidden" name="paid" id="paid" value="0">
          <input type="hidden" name="client_id" id="client_id_partial">
          <label for="amount">Amount</label>
          <input type="number" id="amount" name="amount" class="form-control">
          <label for="date">Date Of Payment</label>
          <input type="date" id="date" name="date_of_payment" class="form-control">

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <input type="submit" class="btn btn-primary" value="Add">
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="makePayment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Make Payment of <span id="paymentAmount"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="#" method="post">
        @csrf
        <input type="hidden" name="paymentId" id="paymentId" >
        <div class="modal-body">
          <div class="row">
            <div class="col-md-4">
              <label for="dateOfPayment">Date Of Payment</label>
              <input type="date" name="dateOfPayment" id ="dateOfPayment" class="form-control">
            </div>
            <div class="col-md-4">
              <label for="paymentAmount">Amount</label>
              <input type="number" name="paymentAmount" id ="paymentAmount2" class="form-control">
            </div>
            <div class="col-md-4">
              <label for="modeOfPayment">Mode of Payment</label>
              <select name="modeOfPayment" id="modeOfPayment" class="form-control">
                <option value="">--Select--</option>
                <option value="Card">Card</option>
                <option value="Online">Online</option>
                <option value="Bank Transfer">Bank Transfer</option>
                <option value="Cheque">Cheque</option>
              </select>
            </div>
          </div>
          <div class="row paymentMode">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Pay</button>
        </div>
      </form>
    </div>
  </div>
</div>


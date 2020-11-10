<div class="col-md-12">
  <div class="col-md-12">
    <h4>Booking Requests</h4>
  </div>
  <hr>
  <div class="row">
    @foreach($client->bookings as $booking)
      <div class="col-md-6">
        <div class="card card-box">
          <div class="card-head">
            <div class="row text-center">
              <div class="col-md-4 pull-left">
                <a href="{{ route('view.client',['slug'=>$booking->client->slug]) }}" target="_blank"><i class="fa fa-2x fa-user-circle" aria-hidden="true"></i></a>
              </div>
              <div class="col-md-4 center"><h4 class="font-weight-bold"><b>{{ $booking->client->name }}</b></h4></div>

            </div>
            <div class="row text-center">
              <div class="col-md-4 center pull-left">Booking Date<br><h5 class="font-weight-bold"><b> {{ \Carbon\Carbon::parse(($booking->bookingRequestDate))->format('l, F jS, Y\\') }} </b></h5></div>
              <div class="col-md-4 center pull-right">Travel Date<br><h5 class="font-weight-bold"><b> {{ \Carbon\Carbon::parse(($booking->travelDate))->format('l, F jS, Y\\') }}</b></h5></div>
              <div class="col-md-4 center pull-right">Enrollment Date<br><h5 class="font-weight-bold"><b>{{ \Carbon\Carbon::parse(($booking->client->latestPackage->enrollmentDate))->format('l, F jS, Y\\') }}</b></h5></div>
            </div>
          </div>
          @php
            $package = $booking->client->latestPackage;
          @endphp
          <div class="row text-center">
            <div class="col-md-3">FCLP <br><strong>{{ $package->productName }}({{ $package->productCost }})</strong></div>
            <div class="col-md-3">Amount Paid <br><strong>{{ $booking->client->paidAmount }}</strong></div>
            <div class="col-md-3">Amount Pending <br><strong>{{ $package->productCost}}</strong></div>
            <div class="col-md-3">Charges <br><strong>0</strong></div>
          </div>
          <hr>
          <div class="card-body ">
            <div class="row text-center">
              <div class="col-md-3">
                Nights <br> <b>{{ $booking->totalNights }}</b>
              </div>
              <div class="col-md-3">
                Holiday <br> <b>{{ $booking->holidayType }}</b>
              </div>
              <div class="col-md-3">
                Breakfast <br> <b>{{ $booking->breakfast ? 'Yes' : 'No' }}</b>
              </div>
              <div class="col-md-3">
                Eligible <br> <b>{{ $booking->eligible ? 'Yes':'No'}}</b>
              </div>
            </div>

            @foreach($booking->BookingInfo as $info)
              <hr>
              <div class="row center">
                <div class="col-md-12 text-center"><h5 class="font-weight-bold"><b>  {{ $info->destination }}  </b></h5></div>
              </div>
              <div class="row center">
                <div class="col-md-4">Pax <br> <b>{{ $info->adults }} Adults + {{ $info->kids }} Kids <br>   </b>
                </div>
                <div class="col-md-4">Check In <br> <b>   {{ $info->check_in }}</b> </div>
                <div class="col-md-4">Check Out <br> <b> {{ $info->check_out }} </b> </div>
              </div>
            @endforeach


            <hr>
            <strong> Booking Added By:{{ $booking->employee->name }}</strong>   <br>
            <strong>Booking Remarks: {{ $booking->remarks }}</strong>
            <hr>


            @if($booking->status == NULL)
              <form action="{{ route('update.booking.status',['bookingId'=>$booking->id]) }}"  method="post">
                @csrf
                <div class="row">
                  <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                  <div class="col-md-12">
                    <select name="status" id="booking_status" required class="form-control">
                      <option value="">--Select Status--</option>
                      <option value="approved">Approve</option>
                      <option value="rejected">Reject</option>
                    </select>
                    <br>
                    <div >
                      <textarea placeholder="Enter Remarks..." name="remarks" class="form-control" required></textarea>
                      <br>
                      <div class="text-center">
                        <button class="btn btn-primary" type="submit">Update</button>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            @else
              <hr>
              @if($booking->status == 'approved')
                <strong>Approved By: {{ \App\User::find($booking->statusUpdatedBy)->name }}</strong> <br>
                <strong>Approved On: {{ Carbon\Carbon::parse($booking->statusUpdatedOn)->format('D d, Y h:i A') }}</strong> <br>
                <strong>Remarks: {{ $booking->statusRemarks }}</strong>

              @elseif($booking->status == 'rejected')
                <strong>Rejected By: {{ \App\User::find($booking->statusUpdatedBy)->name }}</strong> <br>
                <strong>Rejected On: {{ Carbon\Carbon::parse($booking->statusUpdatedOn)->format('D d, Y h:i A') }}</strong> <br>
                <strong>Remarks: {{ $booking->statusRemarks }}</strong>
              @endif
            @endif


          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>
<div class="col-md-12">


    @if($client->ClientHoliday->count())
      <div class="row">
        <div class="col-md-12">
          <div class="card card-box">
            <div class="card-head">
              <div class="col-sm-12 center">
                <h4 class="font-weight-bold">
                  <b>Holiday Packages</b>
                </h4>
              </div>
            </div>
          </div>
        </div>
        @foreach($client->ClientHoliday as $ch)
          <div class="col-md-4">
            <div class="card card-box" @if($ch->cancelled) style="background-color: #FCC8C8" @endif>
              <div class="card-head">
                <div class="col-md-3">
                </div>
                <div class="col-md-4 center"><h4 class="font-weight-bold"><b>{{ $ch->destination }}</b></h4></div>
                <div class="col-md-3 pull-right">
                  @if(!$ch->cancelled)
                    <span class="dropdown">
                                        <button class="btn btn-danger" onclick="sendThis({{ $ch->id }})" data-toggle="modal" data-target="#cancelHoliday">
                                                    <i class="fa fa-trash-o" style="color:white"></i>
                                                </button>
                                    </span>
                  @endif
                </div>
              </div>
              @if($ch->cancelled)
                <div class="text-center">
                  Holiday Cancelled by <strong>{{ App\User::find($ch->cancelled_by)->name }}</strong> on <strong>{{ Carbon\Carbon::parse($ch->cancelled_on)->format('F d, Y h:i A') }}</strong>
                </div>
              @endif
              <div class="modal fade" id="cancelHoliday" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <form action="{{ route('cancel.holiday') }}" method="post">
                      @csrf
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Cancel Holiday</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <input type="hidden" name="id" id="myId" value="{{ $ch->id }}" >
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
                                      $holidayAmountPaidByClient += $details->amount_paid_by_client;
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
                  <strong>Verification Remarks:</strong> {{$b->statusRemarks}}
                  <hr>
                  <strong>Offer Approved By:</strong>
                  @if($b->statusUpdatedBy == Auth::id())
                    {{__('You')}}
                  @else
                    {{App\User::find($b->statusUpdatedBy)->name}}
                  @endif
                  <br>
                  <strong>Approved On:</strong> {{ \Carbon\Carbon::parse($b->statusUpdatedOn)->format('l, F jS, Y\\ h:i A') }} <br>
                  <strong>Approval Remarks:</strong> {{$b->statusRemarks}}
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
<script>
  function partialPayment(id,addOn,clientId){
    $('#holidayDetailsId').val(id);
    $('#addonPartial').val(addOn);
    $('#client_id_partial').val(clientId);
    $('#partialPaymentModel').modal('show');
  }
</script>
<script>
  function makePayment(id,amount,dateOfPayment){
    $('.paymentMode').html('');
    $('#paymentAmount').html(amount);
    $('#paymentAmount2').val(amount);
    $('#paymentId').val(id);
    $('#dateOfPayment').val(dateOfPayment);
    $('#makePayment').modal();
  }
  $('#modeOfPayment').change(function () {
    $('.paymentMode').html('');
    var mode = this.value;
    console.log(mode);
    if(mode == 'Card'){
      var details = '<div class="col-md-4">\n' +
        '                                    <label for="cardBankName">Card Bank Name</label>\n' +
        '                                    <input type="text" id ="cardBankName" name="cardBankName" class="form-control">\n' +
        '                                </div>\n' +
        '                                <div class="col-md-4">\n' +
        '                                    <label for="cardLastFourDigits">Last Four Digits</label>\n' +
        '                                    <input type="number" id ="cardLastFourDigits" name="cardLastFourDigits" class="form-control">\n' +
        '                                </div>\n' +
        '                                <div class="col-md-4">\n' +
        '                                    <label for="cardDescription">Card Description</label>\n' +
        '                                    <input type="text" id ="cardDescription" name="cardDescription" class="form-control">\n' +
        '                                </div>';
      $('.paymentMode').html(details);
    }
    if(mode == 'Online'){
      var details = '   <div class="col-md-4">\n' +
        '                                <label for="onlineBankName">Bank Name</label>\n' +
        '                                <input type="text" name="bankName" id="onlineBankName" class="form-control">\n' +
        '                            </div>';
      $('.paymentMode').html(details);
    }
    if(mode == 'Bank Transfer'){
      var details = '   <div class="col-md-4">\n' +
        '                                <label for="onlineBankName">Bank Name</label>\n' +
        '                                <input type="text" name="bankName" id="onlineBankName" class="form-control">\n' +
        '                            </div>';
      $('.paymentMode').html(details);
    }
    if(mode == 'Cheque'){
      var details = '<div class="col-md-4">\n' +
        '                                <label for="chequeNumber">Cheque Number</label>\n' +
        '                                <input type="text" name="chequeNumber" id="chequeNumber" class="form-control">\n' +
        '                            </div>';
      $('.paymentMode').html(details);
    }

  });
</script>

<script>
  function chaljaPlease(foo){
    var chd =  JSON.parse($(foo).find('.chd').val());
    var pcht =  JSON.parse($(foo).find('.pcht').val());
    var ucht =  JSON.parse($(foo).find('.ucht').val());

    if(chd["hotel_name"] != null){
      var hotel =
        '<div class="row">'+
        '<div class="col-md-2"></div>'+
        '<div class="col-md-4">'+
        '<strong>Nights:</strong>'+
        '</div>'+
        '<div class="col-md-4">'+
        chd["nights"]+
        '</div>'+
        '</div>'+
        '<div class="row">'+
        '<div class="col-md-2"></div>'+
        '<div class="col-md-4">'+
        '<strong>Hotel Name:</strong>'+
        '</div>'+
        '<div class="col-md-4">'+
        chd["hotel_name"]+
        '</div>'+
        '</div>'+
        '<div class="row">'+
        '<div class="col-md-2"></div>'+
        '<div class="col-md-4">'+
        '<strong>Check In:</strong>'+
        '</div>'+
        '<div class="col-md-4">'+
        chd["check_in"]+
        '</div>'+
        '</div>'+
        '<div class="row">'+
        '<div class="col-md-2"></div>'+
        '<div class="col-md-4">'+
        '<strong>Check Out:</strong>'+
        '</div>'+
        '<div class="col-md-4">'+
        chd["check_out"]+
        '</div>'+
        '</div>'+
        '<div class="row">'+
        '<div class="col-md-2"></div>'+
        '<div class="col-md-4">'+
        '<strong>Pax:</strong>'+
        '</div>'+
        '<div class="col-md-4">'+
        chd["pax"]+
        '</div>'+
        '</div>'
    }else{
      var hotel = ''
    }

    if(chd["flight_pax"] != null){
      var flight =
        '<div class="row">'+
        '<div class="col-md-2"></div>'+
        '<div class="col-md-4">'+
        '<strong>Pax:</strong>'+
        '</div>'+
        '<div class="col-md-4">'+
        chd["flight_pax"]+
        '</div>'+
        '</div>'+
        '<div class="row">'+
        '<div class="col-md-2"></div>'+
        '<div class="col-md-4">'+
        '<strong>Flight Details:</strong>'+
        '</div>'+
        '<div class="col-md-4">'+
        chd["flight_details"]+
        '</div>'+
        '</div>'
    }else{
      var flight = ''
    }

    if(chd["add_on"] == 1){
      var add_on = ' (Add On)';
      var amount =
        '<div class="row">'+
        '<div class="col-md-2"></div>'+
        '<div class="col-md-4">'+
        '<strong>Service Price:</strong>'+
        '</div>'+
        '<div class="col-md-4">'+
        chd["add_on_service_price"]+
        '</div>'+
        '</div>'+
        '<div class="row">'+
        '<div class="col-md-2"></div>'+
        '<div class="col-md-4">'+
        '<strong>Paid By Client:</strong>'+
        '</div>'+
        '<div class="col-md-4">'+
        chd["amount_paid_by_client"]+
        '</div>'+
        '</div>'

    }else{
      var add_on = '';
      var amount =
        '<div class="row">'+
        '<div class="col-md-2"></div>'+
        '<div class="col-md-4">'+
        '<strong>Vendor Price:</strong>'+
        '</div>'+
        '<div class="col-md-4">'+
        chd["vendor_price"]+
        '</div>'+
        '</div>'+
        '<div class="row">'+
        '<div class="col-md-2"></div>'+
        '<div class="col-md-4">'+
        '<strong>Our Price:</strong>'+
        '</div>'+
        '<div class="col-md-4">'+
        chd["our_price"]+
        '</div>'+
        '</div>'

    }

    var prows = '';

    for (let i = 0; i < pcht.length ; i++) {
      temp = '<tr>'+
        '<td>'+ pcht[i]["date_of_payment"] +'</td>'+
        '<td>'+ pcht[i]["amount"] +'</td>'+
        '<td>'+ pcht[i]["mode_of_payment"] +'</td>'+
        '<td>'+ pcht[i]["last_four_card_digits"] +'</td>'+
        '<td>'+ pcht[i]["card_description"] +'</td>'+
        '<td>'+ pcht[i]["bank_name"] +'</td>'+
        '<td>'+ pcht[i]["cheque_number"] +'</td>'+
        '<td>'+
        '<a href="javascript:void(0)" class="btn btn-sm btn-info" onclick="editPastTransaction(this)">Edit'+
        '<input type="hidden" class="date_of_payment" value="'+pcht[i]["date_of_payment"]+'">'+
        '<input type="hidden" class="amount" value="'+pcht[i]["amount"]+'">'+
        '<input type="hidden" class="mode_of_payment" value="'+pcht[i]["mode_of_payment"]+'">'+
        '<input type="hidden" class="last_four_card_digits" value="'+pcht[i]["last_four_card_digits"]+'">'+
        '<input type="hidden" class="card_description" value="'+pcht[i]["card_description"]+'">'+
        '<input type="hidden" class="bank_name" value="'+pcht[i]["bank_name"]+'">'+
        '<input type="hidden" class="cheque_number" value="'+pcht[i]["cheque_number"]+'">'+
        '<input type="hidden" class="id" value="'+pcht[i]["id"]+'">'+
        '</a>'+
        '</td>'+
        '</tr>'

      prows = prows + temp;
    }

    var past =
      '<div class="row">'+
      '<div class="col-md-12">'+
      '<table class="table table-bordered">'+
      '<thead>'+
      '<tr>'+
      '<th>Date Of Payment</th>'+
      '<th>Amount</th>'+
      '<th>Mode Of Payment</th>'+
      '<th>Last Four Card Digits</th>'+
      '<th>Card Description</th>'+
      '<th>Bank Name</th>'+
      '<th>Cheque Number</th>'+
      '<th>Action</th>'+
      '</tr>'+
      '</thead>'+
      '<tbody>'+
      prows+
      '</tbody>'+
      '</table>'+
      '</div>'+
      '</div>'

    var urows = '';

    for (let i = 0; i < ucht.length ; i++) {
      var foo = JSON.stringify(ucht)
      temp = '<tr>'+
        '<td>'+ ucht[i]["date_of_payment"] +'</td>'+
        '<td>'+ ucht[i]["amount"] +'</td>'+
        '<td>'+
        '<a href="javascript:void(0)" class="btn btn-sm btn-info" onclick="editUpcomingTransaction(this)">Edit'+
        '<input type="hidden" class="date_of_payment" value="'+ucht[i]["date_of_payment"]+'">'+
        '<input type="hidden" class="amount" value="'+ucht[i]["amount"]+'">'+
        '<input type="hidden" class="id" value="'+ucht[i]["id"]+'">'+
        '</a>'+
        '<a href="javascript:void(0)" class="btn btn-sm btn-success" onclick="payUpcoming(this)">Pay'+
        '<input type="hidden" class="date_of_payment" value="'+ucht[i]["date_of_payment"]+'">'+
        '<input type="hidden" class="amount" value="'+ucht[i]["amount"]+'">'+
        '<input type="hidden" class="mode_of_payment" value="'+ucht[i]["mode_of_payment"]+'">'+
        '<input type="hidden" class="last_four_card_digits" value="'+ucht[i]["last_four_card_digits"]+'">'+
        '<input type="hidden" class="card_description" value="'+ucht[i]["card_description"]+'">'+
        '<input type="hidden" class="bank_name" value="'+ucht[i]["bank_name"]+'">'+
        '<input type="hidden" class="cheque_number" value="'+ucht[i]["cheque_number"]+'">'+
        '<input type="hidden" class="id" value="'+ucht[i]["id"]+'">'+
        '</a>'+
        '</td>'+
        '</tr>'

      urows = urows + temp;
    }

    var upcoming =
      '<div class="row">'+
      '<div class="col-md-12">'+
      '<table class="table table-bordered">'+
      '<thead>'+
      '<tr>'+
      '<th>Date Of Payment</th>'+
      '<th>Amount</th>'+
      '<th>Action</th>'+
      '</tr>'+
      '</thead>'+
      '<tbody>'+
      urows+
      '</tbody>'+
      '</table>'+
      '</div>'+
      '</div>'

    var modal =
      '<div id="modalBhaiHamare" class="modal fade" role="dialog">'+
      '<div class="modal-dialog">'+
      '<div class="modal-content" style="width:700px !important;">'+
      '<div class="modal-header bg-light">'+
      '<h5 class="modal-title" id="exampleModalLongTitle" ><strong>'+ chd["service_type"] + add_on +'</strong></h5>'+
      '</div>'+
      '<div class="modal-body">'+
      '<div class="row">'+
      '<div class="col-md-2"></div>'+
      '<div class="col-md-4">'+
      '<strong>Vendor:</strong> '+
      '<button class="btn btn-sm btn-warning" onclick="editVendorDetails('+chd['id']+',\'' + chd['vendor_name'] +'\','+ chd['vendor_price'] + ','+ chd['our_price']+')"><span class="fa fa-pencil"></span></button>'+
      '</div>'+
      '<div class="col-md-4">'+
      chd["vendor_name"]+
      '</div>'+
      '</div>'+
      '<div class="row">'+
      '<div class="col-md-2"></div>'+
      '<div class="col-md-4">'+
      '<strong>Destination:</strong>'+
      '</div>'+
      '<div class="col-md-4">'+
      chd["destination"]+
      '</div>'+
      '</div>'+
      hotel+
      flight+
      amount+
      '<div class="row">'+
      '<div class="col-md-2"></div>'+
      '<div class="col-md-4">'+
      '<strong>Remarks:</strong>'+
      '</div>'+
      '<div class="col-md-4">'+
      chd["remarks"]+
      '</div>'+
      '</div>'+
      '<hr>'+
      '<div class="text-center"><h3>Past Transactions</h3></div>'+
      past+
      '<hr>'+
      '<div class="text-center"><h3>Upcoming Transactions</h3></div>'+
      upcoming+
      '</div>'+
      '<div class="modal-footer bg-light">'+
      '<button type="button" class="btn btn-primary" data-dismiss="modal">Okay!</button>'+
      '</div>'+
      '</div>'+
      '</div>'+
      '</div>';
    $('#modalDaalo2').html(modal);
    $('#modalBhaiHamare').modal();
  }
</script>



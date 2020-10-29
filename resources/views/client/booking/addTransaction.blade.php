@extends('layouts/contentLayoutMaster')

@section('title', 'Booking Requests')

@section('vendor-style')
  {{-- vendor css files --}}
@endsection
@section('content')

  @include('client.booking.navbar')

<div class="row">
  <div class="col-md-12">
    <form action="{{ route('booking.convert') }}" method="post" id="convertIt">
      @csrf
      <input type="hidden" name="booking_id" value="{{$booking->id}}">
      <div class="row" onmouseover="verifyPlease()";>
        <div class="col-md-12">

          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-md-4">
                  <label for="holiday_type">Holiday Type</label>
                  <input type="text" readonly name="holiday_type" value="{{$bo->holiday_type}}" class="form-control">
                </div>
                <div class="col-md-4">
                  <label for="date_of_travel" class="pull-left">Date of Travel</label>
                  <input type="date" id="date_of_travel" value="{{$bo->date_of_travel}}" name="date_of_travel" class="form-control" required/>
                </div>
                <div class="col-md-4">
                  <label for="offer_destination" class="pull-left">Destination</label>
                  <input type="text" id="destination" name="offer_destination" value="{{$bo->destination}}" class="form-control" required />
                </div>
              </div>
            </div>
          </div>

          <div class="panel">
            <div class="panel-body">
              @foreach($boi as $am)
                @if($am->service_type == 'Hotel')
                  <div class="card">
                    <input type="hidden" name="verify_token[]" value="{{Illuminate\Support\Str::random(16)}}" class="verify_token">
                    <div class="card-body">
                      <div class="row">
                        <input type="hidden" name="add_on[]" value="0">
                        <div class="col-md-4">
                          <label for="service_type">Service Type</label>
                          <select readonly name="service_type[]" class="form-control" required>
                            <option value="">--Select Service Type--</option>
                            <option value="Hotel" selected>Hotel</option>
                          </select>
                        </div>
                        <input type="hidden" name="add_on_service_price[]">
                        <input type="hidden" name="amount_paid_by_client[]">
                        <div class="col-md-4">
                          <label for="vendor">Vendor</label>
                          <input type="text" class="form-control" value="{{$am->vendor_name}}" required name="vendor[]" id="vendor" placeholder="Vendor Name" >
                        </div>
                        <div class="col-md-4">
                          <label for="nights" class="pull-left">Destination</label>
                          <input type="text" name="destination[]" value="{{$am->destination}}" class="form-control" required />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label for="nights" class="pull-left">Nights</label>
                          <input type="number"  name="nights[]" value="{{$am->nights}}" class="form-control max-nights nights" required />
                        </div>
                        <div class="col-md-3">
                          <label for="hotel_name" class="pull-left">Hotel Name</label>
                          <input type="text" id="hotel_name" value="{{$am->hotel_name}}" name="hotel_name[]" class="form-control" required />
                        </div>
                        <div class="col-md-3">
                          <label for="check_in_date" class="pull-left">Check-In Date</label>
                          <input type="date" id="check_in_date" name="check_in[]" value="{{$am->check_in}}"  id="datetimepicker4"class="form-control" required />
                        </div>
                        <div class="col-md-3">
                          <label for="check_out_date" class="pull-left">Check-Out Date</label>
                          <input type="date" id="check_out_date" name="check_out[]" value="{{$am->check_out}}"  id="datetimepicker4" value="" class="form-control" required />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-4">
                          <label for="pax" class="pull-left">Pax</label>
                          <input type="number"  name="pax[]" value="{{$am->pax}}"  class="form-control" required />
                        </div>
                        <div class="col-md-4">
                          <label for="vendor_price" class="pull-left">Vendor Price</label>
                          <input type="number"  name="vendor_price[]" value="{{$am->vendor_price}}"  class="form-control vendorPrice" required />
                        </div>
                        <div class="col-md-4">
                          <label for="our_price" class="pull-left">Our Price</label>
                          <input type="number"  name="our_price[]" value="{{$am->our_price}}"  class="form-control" required />
                        </div>
                      </div>
                      <div class="addMoreTransaction">
                        <div class="transaction">
                          <input type="hidden" name="verify[]" class="verify">
                          <div class="row">
                            <div class="col-md-5">
                              <label for="price">Amount</label>
                              <input type="text" class="form-control" required name="amount[]" max="{{$am->our_price}}" >
                            </div>
                            <div class="col-md-5">
                              <label for="date">Date of Payment</label>
                              <input type="date" class="form-control" required name="date_of_payment[]" onchange="checkDate(this);">
                            </div>
                            <div class="col-md-2">
                              <label>&nbsp;</label><br>
                              <a href="javascript:void(0)" class="btn btn-info" onclick="addMoreTransaction(this)">Add New</a>
                            </div>
                          </div>
                          <div class="checkDate"></div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <label for="remarks">Remarks</label>
                          <textarea class="form-control" name="remarks[]" placeholder="Enter Remarks" id="remarks">{{$am->remarks}}</textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                @elseif($am->service_type == 'Flight')
                  <div class="card">
                    <input type="hidden" name="verify_token[]" value="{{Illuminate\Support\Str::random(16)}}" class="verify_token">
                    <div class="card-body">
                      <div class="row">
                        <input type="hidden" name="add_on[]" value="0">
                        <div class="col-md-4">
                          <label for="service_type">Service Type</label>
                          <select name="service_type[]" readonly class="form-control" required>
                            <option value="">--Select Service Type--</option>
                            <option value="Flight" selected>Flight</option>
                          </select>
                        </div>
                        <input type="hidden" name="add_on_service_price[]">
                        <input type="hidden" name="amount_paid_by_client[]">
                        <div class="col-md-4">
                          <label for="vendor">Vendor</label>
                          <input type="text" class="form-control" value="{{$am->vendor_name}}" required name="vendor[]" id="vendor" placeholder="Vendor Name" >
                        </div>
                        <div class="col-md-4">
                          <label for="nights" class="pull-left">Destination</label>
                          <input type="text" name="destination[]" value="{{$am->destination}}" class="form-control" required />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label for="flight_pax" class="pull-left">Pax</label>
                          <input type="number" name="flight_pax[]" value="{{$am->flight_pax}}" class="form-control" required />
                        </div>
                        <div class="col-md-3">
                          <label for="flight_details" class="pull-left">Flight Details</label>
                          <input type="text" name="flight_details[]" value="{{$am->flight_details}}" class="form-control" required />
                        </div>
                        <div class="col-md-3">
                          <label for="vendor_price" class="pull-left">Vendor Price</label>
                          <input type="number"  name="vendor_price[]" value="{{$am->vendor_price}}"  class="form-control" required />
                        </div>
                        <div class="col-md-3">
                          <label for="our_price" class="pull-left">Our Price</label>
                          <input type="number"  name="our_price[]" value="{{$am->our_price}}"  class="form-control" required />
                        </div>
                      </div>
                      <div class="addMoreTransaction">
                        <div class="transaction">
                          <input type="hidden" name="verify[]" class="verify">
                          <div class="row">
                            <div class="col-md-5">
                              <label for="price">Amount</label>
                              <input type="text" class="form-control" required name="amount[]">
                            </div>
                            <div class="col-md-5">
                              <label for="date">Date of Payment</label>
                              <input type="date" class="form-control" required name="date_of_payment[]" onchange="checkDate(this);">
                            </div>
                            <div class="col-md-2">
                              <label>&nbsp;</label><br>
                              <a href="javascript:void(0)" class="btn btn-info" onclick="addMoreTransaction(this)">Add New</a>
                            </div>
                          </div>
                          <div class="checkDate"></div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <label for="remarks">Remarks</label>
                          <textarea class="form-control" name="remarks[]" placeholder="Enter Remarks" id="remarks">{{$am->remarks}}</textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                @elseif($am->service_type == 'Visa')
                  <div class="card">
                    <input type="hidden" name="verify_token[]" value="{{Illuminate\Support\Str::random(16)}}" class="verify_token">
                    <div class="card-body">
                      <div class="row">
                        <input type="hidden" name="add_on[]" value="0">
                        <div class="col-md-4">
                          <label for="service_type">Service Type</label>
                          <select name="service_type[]" readonly  class="form-control" required>
                            <option value="">--Select Service Type--</option>
                            <option value="Visa" selected>Visa</option>
                          </select>
                        </div>
                        <input type="hidden" name="add_on_service_price[]">
                        <input type="hidden" name="amount_paid_by_client[]">
                        <div class="col-md-4">
                          <label for="vendor">Vendor</label>
                          <input type="text" class="form-control" required name="vendor[]" value="{{$am->vendor_name}}" id="vendor" placeholder="Vendor Name" >
                        </div>
                        <div class="col-md-4">
                          <label for="nights" class="pull-left">Destination</label>
                          <input type="text" name="destination[]" value="{{$am->destination}}" class="form-control" required />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <label for="vendor_price" class="pull-left">Vendor Price</label>
                          <input type="number"  name="vendor_price[]" value="{{$am->vendor_price}}"  class="form-control" required />
                        </div>
                        <div class="col-md-6">
                          <label for="our_price" class="pull-left">Our Price</label>
                          <input type="number"  name="our_price[]" value="{{$am->our_price}}"  class="form-control" required />
                        </div>
                      </div>
                      <div class="addMoreTransaction">
                        <div class="transaction">
                          <input type="hidden" name="verify[]" class="verify">
                          <div class="row">
                            <div class="col-md-5">
                              <label for="price">Amount</label>
                              <input type="text" class="form-control" required name="amount[]">
                            </div>
                            <div class="col-md-5">
                              <label for="date">Date of Payment</label>
                              <input type="date" class="form-control" required name="date_of_payment[]" onchange="checkDate(this);">
                            </div>
                            <div class="col-md-2">
                              <label>&nbsp;</label><br>
                              <a href="javascript:void(0)" class="btn btn-info" onclick="addMoreTransaction(this)">Add New</a>
                            </div>
                          </div>
                          <div class="checkDate"></div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <label for="remarks">Remarks</label>
                          <textarea class="form-control" name="remarks[]" placeholder="Enter Remarks" id="remarks">{{$am->remarks}}</textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                @elseif($am->service_type == 'Land Package/Transfer')
                  <div class="card">
                    <input type="hidden" name="verify_token[]" value="{{Illuminate\Support\Str::random(16)}}" class="verify_token">
                    <div class="card-body">
                      <div class="row">
                        <input type="hidden" name="add_on[]" value="0">
                        <div class="col-md-4">
                          <label for="service_type">Service Type</label>
                          <select readonly name="service_type[]" class="form-control" required>
                            <option value="">--Select Service Type--</option>
                            <option value="Land Package/Transfer" selected>Land Package/Transfer</option>
                          </select>
                        </div>
                        <input type="hidden" name="add_on_service_price[]">
                        <input type="hidden" name="amount_paid_by_client[]">
                        <div class="col-md-4">
                          <label for="vendor">Vendor</label>
                          <input type="text" class="form-control" value="{{$am->vendor_name}}" required name="vendor[]" id="vendor" placeholder="Vendor Name" >
                        </div>
                        <div class="col-md-4">
                          <label for="nights" class="pull-left">Destination</label>
                          <input type="text" name="destination[]" value="{{$am->destination}}" class="form-control" required />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label for="nights" class="pull-left">Nights</label>
                          <input type="number"  name="nights[]" value="{{$am->nights}}" class="form-control max-nights" required />
                        </div>
                        <div class="col-md-3">
                          <label for="hotel_name" class="pull-left">Hotel Name</label>
                          <input type="text" id="hotel_name" value="{{$am->hotel_name}}" name="hotel_name[]" class="form-control" required />
                        </div>
                        <div class="col-md-3">
                          <label for="check_in_date" class="pull-left">Check-In Date</label>
                          <input type="date" id="check_in_date" name="check_in[]" value="{{$am->check_in}}"  id="datetimepicker4"class="form-control" required />
                        </div>
                        <div class="col-md-3">
                          <label for="check_out_date" class="pull-left">Check-Out Date</label>
                          <input type="date" id="check_out_date" name="check_out[]" value="{{$am->check_out}}"  id="datetimepicker4" value="" class="form-control" required />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-4">
                          <label for="pax" class="pull-left">Pax</label>
                          <input type="number"  name="pax[]" value="{{$am->pax}}"  class="form-control" required />
                        </div>
                        <div class="col-md-4">
                          <label for="vendor_price" class="pull-left">Vendor Price</label>
                          <input type="number"  name="vendor_price[]" value="{{$am->vendor_price}}"  class="form-control" required />
                        </div>
                        <div class="col-md-4">
                          <label for="our_price" class="pull-left">Our Price</label>
                          <input type="number"  name="our_price[]" value="{{$am->our_price}}"  class="form-control" required />
                        </div>
                      </div>
                      <div class="addMoreTransaction">
                        <div class="transaction">
                          <input type="hidden" name="verify[]" class="verify">
                          <div class="row">
                            <div class="col-md-5">
                              <label for="price">Amount</label>
                              <input type="text" required class="form-control" name="amount[]">
                            </div>
                            <div class="col-md-5">
                              <label for="date">Date of Payment</label>
                              <input type="date" required class="form-control" name="date_of_payment[]" onchange="checkDate(this);">
                            </div>
                            <div class="col-md-2">
                              <label>&nbsp;</label><br>
                              <a href="javascript:void(0)" class="btn btn-info" onclick="addMoreTransaction(this)">Add New</a>
                            </div>
                          </div>
                          <div class="checkDate"></div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <label for="remarks">Remarks</label>
                          <textarea class="form-control" name="remarks[]" placeholder="Enter Remarks" id="remarks">{{$am->remarks}}</textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                @elseif($am->service_type == 'Insurance')
                  <div class="card">
                    <input type="hidden" name="verify_token[]" value="{{Illuminate\Support\Str::random(16)}}" class="verify_token">
                    <div class="card-body">
                      <div class="row">
                        <input type="hidden" name="add_on[]" value="0">
                        <div class="col-md-4">
                          <label for="service_type">Service Type</label>
                          <select name="service_type[]" readonly  class="form-control" required>
                            <option value="">--Select Service Type--</option>
                            <option value="Insurance" selected>Insurance</option>
                          </select>
                        </div>
                        <input type="hidden" name="add_on_service_price[]">
                        <input type="hidden" name="amount_paid_by_client[]">
                        <div class="col-md-4">
                          <label for="vendor">Vendor</label>
                          <input type="text" class="form-control" required name="vendor[]" value="{{$am->vendor_name}}" id="vendor" placeholder="Vendor Name" >
                        </div>
                        <div class="col-md-4">
                          <label for="nights" class="pull-left">Destination</label>
                          <input type="text" name="destination[]" value="{{$am->destination}}" class="form-control" required />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <label for="vendor_price" class="pull-left">Vendor Price</label>
                          <input type="number"  name="vendor_price[]" value="{{$am->vendor_price}}"  class="form-control" required />
                        </div>
                        <div class="col-md-6">
                          <label for="our_price" class="pull-left">Our Price</label>
                          <input type="number"  name="our_price[]" value="{{$am->our_price}}"  class="form-control" required />
                        </div>
                      </div>
                      <div class="addMoreTransaction">
                        <div class="transaction">
                          <input type="hidden" name="verify[]" class="verify">
                          <div class="row">
                            <div class="col-md-5">
                              <label for="price">Amount</label>
                              <input type="text" required class="form-control" name="amount[]">
                            </div>
                            <div class="col-md-5">
                              <label for="date">Date of Payment</label>
                              <input type="date" required class="form-control" name="date_of_payment[]" onchange="checkDate(this);">
                            </div>
                            <div class="col-md-2">
                              <label>&nbsp;</label><br>
                              <a href="javascript:void(0)" class="btn btn-info" onclick="addMoreTransaction(this)">Add New</a>
                            </div>
                          </div>
                          <div class="checkDate"></div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <label for="remarks">Remarks</label>
                          <textarea class="form-control" name="remarks[]" placeholder="Enter Remarks" id="remarks">{{$am->remarks}}</textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                @endif
              @endforeach
            </div>
          </div>

          <div class="panel">
            <div class="panel-header">
              <div class="text-center">
                <h3>ADD ON SERVICES</h3><hr>
              </div>
            </div>
            <div class="panel-body">
              @foreach($add_on as $ao)
                @if($ao->service_type == 'Hotel')
                  <div class="card">
                    <input type="hidden" name="verify_token[]" value="{{Illuminate\Support\Str::random(16)}}" class="verify_token">
                    <div class="card-body">
                      <div class="row">
                        <input type="hidden" name="add_on[]" value="1">
                        <div class="col-md-4">
                          <label for="service_type">Service Type</label>
                          <select readonly name="service_type[]" class="form-control" required>
                            <option value="">--Select Service Type--</option>
                            <option value="Hotel" selected>Hotel</option>
                          </select>
                        </div>
                        <input type="hidden" name="vendor_price[]">
                        <input type="hidden" name="our_price[]">
                        <div class="col-md-4">
                          <label for="vendor">Vendor</label>
                          <input type="text" class="form-control" value="{{$ao->vendor_name}}" required name="vendor[]" id="vendor" placeholder="Vendor Name" >
                        </div>
                        <div class="col-md-4">
                          <label for="nights" class="pull-left">Destination</label>
                          <input type="text" name="destination[]" value="{{$ao->destination}}" class="form-control" required />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label for="nights" class="pull-left">Nights</label>
                          <input type="number"  name="nights[]" value="{{$ao->nights}}" class="form-control" required />
                        </div>
                        <div class="col-md-3">
                          <label for="hotel_name" class="pull-left">Hotel Name</label>
                          <input type="text" id="hotel_name" value="{{$ao->hotel_name}}" name="hotel_name[]" class="form-control" required />
                        </div>
                        <div class="col-md-3">
                          <label for="check_in_date" class="pull-left">Check-In Date</label>
                          <input type="date" id="check_in_date" name="check_in[]" value="{{$ao->check_in}}"  id="datetimepicker4"class="form-control" required />
                        </div>
                        <div class="col-md-3">
                          <label for="check_out_date" class="pull-left">Check-Out Date</label>
                          <input type="date" id="check_out_date" name="check_out[]" value="{{$ao->check_out}}"  id="datetimepicker4" value="" class="form-control" required />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-4">
                          <label for="pax" class="pull-left">Pax</label>
                          <input type="number"  name="pax[]" value="{{$ao->pax}}"  class="form-control" required />
                        </div>
                        <div class="col-md-4">
                          <label for="add_on_service_price" class="pull-left">Add On Service Price</label>
                          <input type="number"  name="add_on_service_price[]" value="{{$ao->add_on_service_price}}" onkeyup="setMaxPrice(this);"  class="form-control" required />
                        </div>
                        <div class="col-md-4">
                          <label for="amount_paid_by_client" class="pull-left">Amount Paid By Client</label>
                          <input type="number"  name="amount_paid_by_client[]" value="{{$ao->amount_paid_by_client}}" class="form-control amount-paid" required />
                        </div>
                      </div>
                      <div class="addMoreTransaction">
                        <div class="transaction">
                          <input type="hidden" name="verify[]" class="verify">
                          <div class="row">
                            <div class="col-md-5">
                              <label for="price">Amount</label>
                              <input type="text" required class="form-control" name="amount[]">
                            </div>
                            <div class="col-md-5">
                              <label for="date">Date of Payment</label>
                              <input type="date" required class="form-control" name="date_of_payment[]" onchange="checkDate(this);">
                            </div>
                            <div class="col-md-2">
                              <label>&nbsp;</label><br>
                              <a href="javascript:void(0)" class="btn btn-info" onclick="addMoreTransaction(this)">Add New</a>
                            </div>
                          </div>
                          <div class="checkDate"></div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <label for="remarks">Remarks</label>
                          <textarea class="form-control" name="remarks[]" placeholder="Enter Remarks" id="remarks">{{$ao->remarks}}</textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                @elseif($ao->service_type == 'Flight')
                  <div class="card">
                    <input type="hidden" name="verify_token[]" value="{{Illuminate\Support\Str::random(16)}}" class="verify_token">
                    <div class="card-body">
                      <div class="row">
                        <input type="hidden" name="add_on[]" value="1">
                        <div class="col-md-4">
                          <label for="service_type">Service Type</label>
                          <select name="service_type[]" readonly class="form-control" required>
                            <option value="">--Select Service Type--</option>
                            <option value="Flight" selected>Flight</option>
                          </select>
                        </div>
                        <input type="hidden" name="vendor_price[]">
                        <input type="hidden" name="our_price[]">
                        <div class="col-md-4">
                          <label for="vendor">Vendor</label>
                          <input type="text" class="form-control" value="{{$ao->vendor_name}}" required name="vendor[]" id="vendor" placeholder="Vendor Name" >
                        </div>
                        <div class="col-md-4">
                          <label for="nights" class="pull-left">Destination</label>
                          <input type="text" name="destination[]" value="{{$ao->destination}}" class="form-control" required />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label for="flight_pax" class="pull-left">Pax</label>
                          <input type="number" name="flight_pax[]" value="{{$ao->flight_pax}}" class="form-control" required />
                        </div>
                        <div class="col-md-3">
                          <label for="flight_details" class="pull-left">Flight Details</label>
                          <input type="text" name="flight_details[]" value="{{$ao->flight_details}}" class="form-control" required />
                        </div>
                        <div class="col-md-3">
                          <label for="add_on_service_price" class="pull-left">Add On Service Price</label>
                          <input type="number"  name="add_on_service_price[]" value="{{$ao->add_on_service_price}}" onkeyup="setMaxPrice(this);"  class="form-control" required />
                        </div>
                        <div class="col-md-3">
                          <label for="amount_paid_by_client" class="pull-left">Amount Paid By Client</label>
                          <input type="number"  name="amount_paid_by_client[]" value="{{$ao->amount_paid_by_client}}" class="form-control amount-paid" required />
                        </div>
                      </div>
                      <div class="addMoreTransaction">
                        <div class="transaction">
                          <input type="hidden" name="verify[]" class="verify">
                          <div class="row">
                            <div class="col-md-5">
                              <label for="price">Amount</label>
                              <input type="text" required class="form-control" name="amount[]">
                            </div>
                            <div class="col-md-5">
                              <label for="date">Date of Payment</label>
                              <input type="date" required class="form-control" name="date_of_payment[]" onchange="checkDate(this);">
                            </div>
                            <div class="col-md-2">
                              <label>&nbsp;</label><br>
                              <a href="javascript:void(0)" class="btn btn-info" onclick="addMoreTransaction(this)">Add New</a>
                            </div>
                          </div>
                          <div class="checkDate"></div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <label for="remarks">Remarks</label>
                          <textarea class="form-control" name="remarks[]" placeholder="Enter Remarks" id="remarks">{{$ao->remarks}}</textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                @elseif($ao->service_type == 'Visa')
                  <div class="card">
                    <input type="hidden" name="verify_token[]" value="{{Illuminate\Support\Str::random(16)}}" class="verify_token">
                    <div class="card-body">
                      <div class="row">
                        <input type="hidden" name="add_on[]" value="1">
                        <div class="col-md-4">
                          <label for="service_type">Service Type</label>
                          <select name="service_type[]" readonly  class="form-control" required>
                            <option value="">--Select Service Type--</option>
                            <option value="Visa" selected>Visa</option>
                          </select>
                        </div>
                        <input type="hidden" name="vendor_price[]">
                        <input type="hidden" name="our_price[]">
                        <div class="col-md-4">
                          <label for="vendor">Vendor</label>
                          <input type="text" class="form-control" required name="vendor[]" value="{{$ao->vendor_name}}" id="vendor" placeholder="Vendor Name" >
                        </div>
                        <div class="col-md-4">
                          <label for="nights" class="pull-left">Destination</label>
                          <input type="text" name="destination[]" value="{{$ao->destination}}" class="form-control" required />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <label for="add_on_service_price" class="pull-left">Add On Service Price</label>
                          <input type="number"  name="add_on_service_price[]" value="{{$ao->add_on_service_price}}" onkeyup="setMaxPrice(this);"  class="form-control" required />
                        </div>
                        <div class="col-md-6">
                          <label for="amount_paid_by_client" class="pull-left">Amount Paid By Client</label>
                          <input type="number"  name="amount_paid_by_client[]" value="{{$ao->amount_paid_by_client}}" class="form-control amount-paid" required />
                        </div>
                      </div>
                      <div class="addMoreTransaction">
                        <div class="transaction">
                          <input type="hidden" name="verify[]" class="verify">
                          <div class="row">
                            <div class="col-md-5">
                              <label for="price">Amount</label>
                              <input type="text" required class="form-control" name="amount[]">
                            </div>
                            <div class="col-md-5">
                              <label for="date">Date of Payment</label>
                              <input type="date" required class="form-control" name="date_of_payment[]" onchange="checkDate(this);">
                            </div>
                            <div class="col-md-2">
                              <label>&nbsp;</label><br>
                              <a href="javascript:void(0)" class="btn btn-info" onclick="addMoreTransaction(this)">Add New</a>
                            </div>
                          </div>
                          <div class="checkDate"></div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <label for="remarks">Remarks</label>
                          <textarea class="form-control" name="remarks[]" placeholder="Enter Remarks" id="remarks">{{$ao->remarks}}</textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                @elseif($ao->service_type == 'Land Package/Transfer')
                  <div class="card">
                    <input type="hidden" name="verify_token[]" value="{{Illuminate\Support\Str::random(16)}}" class="verify_token">
                    <div class="card-body">
                      <div class="row">
                        <input type="hidden" name="add_on[]" value="1">
                        <div class="col-md-4">
                          <label for="service_type">Service Type</label>
                          <select readonly name="service_type[]" class="form-control" required>
                            <option value="">--Select Service Type--</option>
                            <option value="Land Package/Transfer" selected>Land Package/Transfer</option>
                          </select>
                        </div>
                        <input type="hidden" name="vendor_price[]">
                        <input type="hidden" name="our_price[]">
                        <div class="col-md-4">
                          <label for="vendor">Vendor</label>
                          <input type="text" class="form-control" value="{{$ao->vendor_name}}" required name="vendor[]" id="vendor" placeholder="Vendor Name" >
                        </div>
                        <div class="col-md-4">
                          <label for="nights" class="pull-left">Destination</label>
                          <input type="text" name="destination[]" value="{{$ao->destination}}" class="form-control" required />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label for="nights" class="pull-left">Nights</label>
                          <input type="number"  name="nights[]" value="{{$ao->nights}}" class="form-control" required />
                        </div>
                        <div class="col-md-3">
                          <label for="hotel_name" class="pull-left">Hotel Name</label>
                          <input type="text" id="hotel_name" value="{{$ao->hotel_name}}" name="hotel_name[]" class="form-control" required />
                        </div>
                        <div class="col-md-3">
                          <label for="check_in_date" class="pull-left">Check-In Date</label>
                          <input type="date" id="check_in_date" name="check_in[]" value="{{$ao->check_in}}"  id="datetimepicker4"class="form-control" required />
                        </div>
                        <div class="col-md-3">
                          <label for="check_out_date" class="pull-left">Check-Out Date</label>
                          <input type="date" id="check_out_date" name="check_out[]" value="{{$ao->check_out}}"  id="datetimepicker4" value="" class="form-control" required />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-4">
                          <label for="pax" class="pull-left">Pax</label>
                          <input type="number"  name="pax[]" value="{{$ao->pax}}"  class="form-control" required />
                        </div>
                        <div class="col-md-4">
                          <label for="add_on_service_price" class="pull-left">Add On Service Price</label>
                          <input type="number"  name="add_on_service_price[]" value="{{$ao->add_on_service_price}}" onkeyup="setMaxPrice(this);"  class="form-control" required />
                        </div>
                        <div class="col-md-4">
                          <label for="amount_paid_by_client" class="pull-left">Amount Paid By Client</label>
                          <input type="number"  name="amount_paid_by_client[]" value="{{$ao->amount_paid_by_client}}" class="form-control amount-paid" required />
                        </div>
                      </div>
                      <div class="addMoreTransaction">
                        <div class="transaction">
                          <input type="hidden" name="verify[]" class="verify">
                          <div class="row">
                            <div class="col-md-5">
                              <label for="price">Amount</label>
                              <input type="text" required class="form-control" name="amount[]">
                            </div>
                            <div class="col-md-5">
                              <label for="date">Date of Payment</label>
                              <input type="date" required class="form-control" name="date_of_payment[]" onchange="checkDate(this);">
                            </div>
                            <div class="col-md-2">
                              <label>&nbsp;</label><br>
                              <a href="javascript:void(0)" class="btn btn-info" onclick="addMoreTransaction(this)">Add New</a>
                            </div>
                          </div>
                          <div class="checkDate"></div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <label for="remarks">Remarks</label>
                          <textarea class="form-control" name="remarks[]" placeholder="Enter Remarks" id="remarks">{{$ao->remarks}}</textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                @elseif($ao->service_type == 'Insurance')
                  <div class="card">
                    <input type="hidden" name="verify_token[]" value="{{Illuminate\Support\Str::random(16)}}" class="verify_token">
                    <div class="card-body">
                      <div class="row">
                        <input type="hidden" name="add_on[]" value="1">
                        <div class="col-md-4">
                          <label for="service_type">Service Type</label>
                          <select name="service_type[]" readonly  class="form-control" required>
                            <option value="">--Select Service Type--</option>
                            <option value="Insurance" selected>Insurance</option>
                          </select>
                        </div>
                        <input type="hidden" name="vendor_price[]">
                        <input type="hidden" name="our_price[]">
                        <div class="col-md-4">
                          <label for="vendor">Vendor</label>
                          <input type="text" class="form-control" required name="vendor[]" value="{{$ao->vendor_name}}" id="vendor" placeholder="Vendor Name" >
                        </div>
                        <div class="col-md-4">
                          <label for="nights" class="pull-left">Destination</label>
                          <input type="text" name="destination[]" value="{{$ao->destination}}" class="form-control" required />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <label for="add_on_service_price" class="pull-left">Add On Service Price</label>
                          <input type="number"  name="add_on_service_price[]" value="{{$ao->add_on_service_price}}" onkeyup="setMaxPrice(this);"  class="form-control" required />
                        </div>
                        <div class="col-md-6">
                          <label for="amount_paid_by_client" class="pull-left">Amount Paid By Client</label>
                          <input type="number"  name="amount_paid_by_client[]" value="{{$ao->amount_paid_by_client}}" class="form-control amount-paid" required />
                        </div>
                      </div>
                      <div class="addMoreTransaction">
                        <div class="transaction">
                          <input type="hidden" name="verify[]" class="verify">
                          <div class="row">
                            <div class="col-md-5">
                              <label for="price">Amount</label>
                              <input type="text" required class="form-control" name="amount[]">
                            </div>
                            <div class="col-md-5">
                              <label for="date">Date of Payment</label>
                              <input type="date" required class="form-control" name="date_of_payment[]" onchange="checkDate(this);">
                            </div>
                            <div class="col-md-2">
                              <label>&nbsp;</label><br>
                              <a href="javascript:void(0)" class="btn btn-info" onclick="addMoreTransaction(this)">Add New</a>
                            </div>
                          </div>
                          <div class="checkDate"></div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <label for="remarks">Remarks</label>
                          <textarea class="form-control" name="remarks[]" placeholder="Enter Remarks" id="remarks">{{$ao->remarks}}</textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                @endif
              @endforeach
            </div>
          </div>

        </div>
      </div>

      <div class="text-center submit">
        <button type="submit" id="submit" class="btn btn-md btn-info">Convert</button>
      </div>
      <div class="text-center submitTwo">

      </div>
    </form>

  </div>
</div>

@endsection



@section('vendor-script')

@endsection
@section('page-script')
  <script>
    function addMoreTransaction(foo){
      var data =
        '<div class="transaction">'+
        '<input type="hidden" name="verify[]" class="verify">'+
        '<div class="row">'+
        '<div class="col-md-5">'+
        '<label for="price">Amount</label>'+
        '<input type="text" required class="form-control" name="amount[]">'+
        '</div>'+
        '<div class="col-md-5">'+
        '<label for="date">Date of Payment</label>'+
        '<input type="date" required class="form-control" name="date_of_payment[]" onchange="checkDate(this);">'+
        '</div>'+
        '<div class="col-md-2">'+
        '<label>&nbsp;</label><br>'+
        '<a href="javascript:void(0)" class="btn btn-danger" onclick="remove(this)">Remove</a>'+
        '</div>'+
        '</div>'+
        '<div class="checkDate"></div>';
      '</div>'+
      $(foo).parents('.addMoreTransaction').append(data);
    }

    function remove(foo){
      $(foo).parents('.transaction').remove();
    }
  </script>

  <script>
    function checkDate(foo){
      var date = new Date();
      var dd = String(date.getDate()).padStart(2, '0');
      var mm = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!
      var yyyy = date.getFullYear();

      var today = yyyy + '-' + mm + '-' + dd;

      if(foo.value < today){
        var data =
          '<div class="row">'+
          '<input type="hidden" name="paid[]" value="1">'+
          '<div class="col-md-4">'+
          '<label for="mode_of_payment">Mode of Payment:</label>' +
          '<select name="mode_of_payment[]" onchange="paymentDetails(this)" class="form-control" required>' +
          '<option value="">---Select---</option>' +
          '<option value="credit_card">Credit Card</option>' +
          '<option value="debit_card">Debit Card</option>' +
          '<option value="bank">Bank Account</option>' +
          '<option value="cheque">Cheque</option>' +
          '<option value="online">Online</option>' +
          '<option value="other">Other</option>' +
          '</select>' +
          '</div>'+
          '</div>' ;

        $(foo).parents('.transaction').find('.checkDate').html(data);
      }else{
        var data =
          '<input type="hidden" name="paid[]" value="0">'+
          '<input type="hidden" name="last_four_card_digits[]" >'+
          '<input type="hidden" name="card_description[]">'+
          '<input type="hidden" name="bank_name[]">'+
          '<input type="hidden" name="cheque_number[]">'+
          '<input type="hidden" name="mode_of_payment[]">';
        $(foo).parents('.transaction').find('.checkDate').html(data);
      }
    }
  </script>

  <script>
    function paymentDetails(foo){
      if(foo.value == 'credit_card'){
        var data =
          '<div class="row">'+
          '<div class="col-md-4">'+
          '<label for="mode_of_payment">Mode of Payment:</label>' +
          '<select name="mode_of_payment[]" onchange="paymentDetails(this)" class="form-control" required>' +
          '<option value="">---Select---</option>' +
          '<option value="credit_card" selected>Credit Card</option>' +
          '<option value="debit_card">Debit Card</option>' +
          '<option value="bank">Bank Account</option>' +
          '<option value="cheque">Cheque</option>' +
          '<option value="online">Online</option>' +
          '<option value="other">Other</option>' +
          '</select>' +
          '</div>'+
          '<div class="col-md-4">'+
          '<label for="last_four_card_digits">Last Four Card Digits:</label>' +
          '<input type="number" maxlength="4" name="last_four_card_digits[]" class="form-control" required>'+
          '</div>'+
          '<div class="col-md-4">'+
          '<label for="card_description">Card Description:</label>' +
          '<input type="text" name="card_description[]" class="form-control" required>'+
          '<input type="hidden" name="bank_name[]">'+
          '<input type="hidden" name="cheque_number[]">'+
          '</div>'+
          '</div>';
      }else if(foo.value == 'debit_card'){
        var data =
          '<div class="row">'+
          '<div class="col-md-4">'+
          '<label for="mode_of_payment">Mode of Payment:</label>' +
          '<select name="mode_of_payment[]" onchange="paymentDetails(this)" class="form-control" required>' +
          '<option value="">---Select---</option>' +
          '<option value="credit_card" >Credit Card</option>' +
          '<option value="debit_card" selected>Debit Card</option>' +
          '<option value="bank">Bank Account</option>' +
          '<option value="cheque">Cheque</option>' +
          '<option value="online">Online</option>' +
          '<option value="other">Other</option>' +
          '</select>' +
          '</div>'+
          '<div class="col-md-4">'+
          '<label for="last_four_card_digits">Last Four Card Digits:</label>' +
          '<input type="number" maxlength="4" name="last_four_card_digits[]" class="form-control" required>'+
          '</div>'+
          '<div class="col-md-4">'+
          '<label for="card_description">Card Description:</label>' +
          '<input type="text" name="card_description[]" class="form-control" required>'+
          '<input type="hidden" name="bank_name[]">'+
          '<input type="hidden" name="cheque_number[]">'+
          '</div>'+
          '</div>';
      }else if(foo.value == 'bank'){
        var data =
          '<div class="row">'+
          '<div class="col-md-6">'+
          '<label for="mode_of_payment">Mode of Payment:</label>' +
          '<select name="mode_of_payment[]" onchange="paymentDetails(this)" class="form-control" required>' +
          '<option value="">---Select---</option>' +
          '<option value="credit_card" >Credit Card</option>' +
          '<option value="debit_card">Debit Card</option>' +
          '<option value="bank" selected>Bank Account</option>' +
          '<option value="cheque">Cheque</option>' +
          '<option value="online">Online</option>' +
          '<option value="other">Other</option>' +
          '</select>' +
          '</div>'+
          '<div class="col-md-6">'+
          '<label for="bank_name">Bank Name:</label>' +
          '<input type="text" name="bank_name[]" class="form-control" required>'+
          '<input type="hidden" name="last_four_card_digits[]" >'+
          '<input type="hidden" name="card_description[]">'+
          '<input type="hidden" name="cheque_number[]">'+
          '</div>'+
          '</div>';
      }else if(foo.value == 'cheque'){
        var data =
          '<div class="row">'+
          '<div class="col-md-6">'+
          '<label for="mode_of_payment">Mode of Payment:</label>' +
          '<select name="mode_of_payment[]" onchange="paymentDetails(this)" class="form-control" required>' +
          '<option value="">---Select---</option>' +
          '<option value="credit_card" >Credit Card</option>' +
          '<option value="debit_card">Debit Card</option>' +
          '<option value="bank" >Bank Account</option>' +
          '<option value="cheque" selected>Cheque</option>' +
          '<option value="online">Online</option>' +
          '<option value="other">Other</option>' +
          '</select>' +
          '</div>'+
          '<div class="col-md-6">'+
          '<label for="cheque_number">Cheque Number:</label>' +
          '<input type="text" name="cheque_number[]" class="form-control" required>'+
          '<input type="hidden" name="last_four_card_digits[]" >'+
          '<input type="hidden" name="card_description[]">'+
          '<input type="hidden" name="bank_name[]">'+
          '</div>'+
          '</div>';
      }else if(foo.value == 'online'){
        var data =
          '<div class="row">'+
          '<div class="col-md-6">'+
          '<label for="mode_of_payment">Mode of Payment:</label>' +
          '<select name="mode_of_payment[]" onchange="paymentDetails(this)" class="form-control" required>' +
          '<option value="">---Select---</option>' +
          '<option value="credit_card" >Credit Card</option>' +
          '<option value="debit_card">Debit Card</option>' +
          '<option value="bank" >Bank Account</option>' +
          '<option value="cheque" >Cheque</option>' +
          '<option value="online" selected>Online</option>' +
          '<option value="other">Other</option>' +
          '</select>' +
          '</div>'+
          '<div class="col-md-6">'+
          '<input type="hidden" name="last_four_card_digits[]" >'+
          '<input type="hidden" name="card_description[]">'+
          '<input type="hidden" name="bank_name[]">'+
          '<input type="hidden" name="cheque_number[]">'+
          '</div>'+
          '</div>';
      }else if(foo.value == 'other'){
        var data =
          '<div class="row">'+
          '<div class="col-md-6">'+
          '<label for="mode_of_payment">Mode of Payment:</label>' +
          '<select name="mode_of_payment[]" onchange="paymentDetails(this)" class="form-control" required>' +
          '<option value="">---Select---</option>' +
          '<option value="credit_card" >Credit Card</option>' +
          '<option value="debit_card">Debit Card</option>' +
          '<option value="bank" >Bank Account</option>' +
          '<option value="cheque" >Cheque</option>' +
          '<option value="online" >Online</option>' +
          '<option value="other" selected>Other</option>' +
          '</select>' +
          '</div>'+
          '<div class="col-md-6">'+
          '<input type="hidden" name="last_four_card_digits[]" >'+
          '<input type="hidden" name="card_description[]">'+
          '<input type="hidden" name="bank_name[]">'+
          '<input type="hidden" name="cheque_number[]">'+
          '</div>'+
          '</div>';
      }else{
        var data =
          '<div class="row">'+
          '<div class="col-md-4">'+
          '<label for="mode_of_payment">Mode of Payment:</label>' +
          '<select name="mode_of_payment[]" onchange="paymentDetails(this)" class="form-control" required>' +
          '<option value="">---Select---</option>' +
          '<option value="credit_card">Credit Card</option>' +
          '<option value="debit_card">Debit Card</option>' +
          '<option value="bank">Bank Account</option>' +
          '<option value="cheque">Cheque</option>' +
          '</select>' +
          '</div>'+
          '</div>';
      }

      $(foo).parents('.transaction').find('.checkDate').html(data);
    }
  </script>

  <script>
    function verifyPlease(){
      var tokens = $('.verify_token');
      for(var i= 0; i < tokens.length; i++){
        var v = tokens[i].value;
        $(tokens[i]).parents('.card').find('.verify').val(v);
      }
    }
  </script>

  @if($bo->holidayType == 'Fully Paid Holiday')
    <script>
      function calculateVendorPrice(){
        var arr = document.getElementsByName('vendor_price[]');
        var total =0;
        for(var i=0; i<arr.length; i++){
          if(parseInt(arr[i].value))
            total += parseInt(arr[i].value);
        }
          @if($booking->client->fully_paid_package_price)
        var val = {{$booking->client->fully_paid_package_price}}
          @else
        var val = {{App\BookingLimitSettings::first()->fully_paid_package_price}}
          @endif
        if(total > val){
          $('.submit').html('<button type="submit" id="submit" class="btn btn-md btn-info">Convert</button>');
        }else{
          $('.submit').html('<button type="submit" id="submit" class="btn btn-md btn-info">Convert</button>');
        }
      }
      function calculateNightPrice(){
        //
      }
    </script>
    @if($booking->client->packages->first()->fclp_name == 'Classic FCV' or $booking->client->packages->first()->fclp_name == 'India FCV')
      <script>
        $('.max-nights').on('keyup',function(){

          var max_nights = document.getElementsByClassName('max-nights');
          var nights = 0;
          for(var i= 0; i < max_nights.length; i++){
            nights = Number(nights) + Number(max_nights[i].value);
          }
          if(nights > 5){
            $('.submit').hide();
            $('.submitTwo').html('<button type="submit" id="submit" onclick="swalItTwo();" class="btn btn-md btn-info">Convert</button>');
          }else{
            $('.submit').show();
            $('.submitTwo').html('');
          }
        });
      </script>
    @endif
  @elseif($bo->holidayType == 'Stay Only Basis Holidays')
    <script>
      function calculateNightPrice(){
        var nights = document.getElementsByClassName('nights');
        var vendor_price = document.getElementsByClassName('vendorPrice');
          @if($booking->client->stay_only_per_night_price)
        var val = '{{$booking->client->stay_only_per_night_price}}';
          @else
        var val = '{{App\BookingLimitSettings::first()->stay_only_per_night_price}}';
          @endif
        var k = 0;
        for(var i= 0; i < nights.length; i++){
          if(vendor_price[i].value > (nights[i].value * val) ){
            k++;
          }
        }

        if( k ){
          $('.submit').html('<button type="submit" id="submit" class="btn btn-md btn-info">Convert</button>');
        }else{
          $('.submit').html('<button type="submit" id="submit" class="btn btn-md btn-info">Convert</button>');
        }


      }
      function calculateVendorPrice(){
        //
      }
    </script>
  @elseif($bo->holidayType == 'Voucher booking')
    <script>
      function calculateNightPrice(){
        var nights = document.getElementsByClassName('nights');
        var vendor_price = document.getElementsByClassName('vendorPrice');
          @if($booking->client->voucher_booking_per_night_price)
        var val = '{{$booking->client->voucher_booking_per_night_price}}';
          @else
        var val = '{{App\BookingLimitSettings::first()->voucher_booking_per_night_price}}';
          @endif

        var k = 0;
        for(var i= 0; i < nights.length; i++){
          if(vendor_price[i].value > (nights[i].value * val) ){
            k++;
          }
        }

        if( k ){
          $('.submit').html('<button type="submit" id="submit" class="btn btn-md btn-info">Convert</button>');
        }else{
          $('.submit').html('<button type="submit" id="submit" class="btn btn-md btn-info">Convert</button>');
        }

      }
      function calculateVendorPrice(){
        //
      }
    </script>
  @elseif($bo->holidayType == 'Offer Nights')
    <script>
      function calculateNightPrice(){
        var nights = document.getElementsByClassName('nights');
        var vendor_price = document.getElementsByClassName('vendorPrice');
          @if($booking->client->offer_night_per_night_price)
        var val = '{{$booking->client->offer_night_per_night_price}}';
          @else
        var val = '{{App\BookingLimitSettings::first()->offer_night_per_night_price}}';
          @endif

        var k = 0;
        for(var i= 0; i < nights.length; i++){
          if(vendor_price[i].value > (nights[i].value * val) ){
            k++;
          }
        }

        if( k ){
          $('.submit').html('<button type="submit" id="submit" class="btn btn-md btn-info">Convert</button>');
        }else{
          $('.submit').html('<button type="submit" id="submit" class="btn btn-md btn-info">Convert</button>');
        }

      }
      function calculateVendorPrice(){
        //
      }
    </script>
  @elseif($bo->holidayType == 'Adjustment')
    <script>
      function calculateNightPrice(){
        $('.submit').html('<button type="submit" id="submit" class="btn btn-md btn-info">Convert</button>');
      }
      function calculateVendorPrice(){

      }
    </script>
  @endif

  <script>
    function swalIt(){
      $('#convertIt').submit();
      swal({
        title: 'Budget Exceeded',
        text: 'Please Adjust Price',
        icon: 'error'
      });
    }

    function swalItTwo(){
      $('#convertIt').submit();
      swal({
        title: 'Please Adjust Nights',
        text: 'Nights Must Be Less Than Or Equal To 5.',
        icon: 'error'
      });
    }
  </script>

  <script>
    setInterval(function(){
      calculateNightPrice();
      calculateVendorPrice();
    }, 2000);
  </script>

@endsection

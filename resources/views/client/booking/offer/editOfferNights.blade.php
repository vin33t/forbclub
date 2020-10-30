@extends('layouts/contentLayoutMaster')

@section('title', $booking->client->name . ' | Booking')

@section('vendor-style')

@endsection
@section('page-style')

@endsection
@section('content')
  @if (count($errors) > 0)
    <div class = "alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
  <div class="row">
      <form @if(!$convert) action="{{ route('update.booking.offer',['bookingId'=>$booking->id]) }}" @else action="{{ route('booking.add.transaction') }}" @endif method="post">
          @csrf
          <input type="hidden" name="booking_id" value="{{$booking->id}}">
          <div class="row">
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

              <div class="card">
                @if($convert)
                  <div class="card-header">
                    <div class="text-right">
                      <input type="checkbox" class="hotel" id=""> Hotel
                      <input type="hidden" name="boi_id[]" value="{{$hotel->id}}">
                    </div>
                  </div>
                @endif
                <div class="card-body">
                  <div class="row">
                    <input type="hidden" name="add_on[]" value="0">
                    <input type="hidden" name="add_more[]" value="0">
                    <div class="col-md-4">
                      <label for="service_type">Service Type</label>
                      <select name="service_type[]" readonly class="form-control" required>
                        <option value="">--Select Service Type--</option>
                        <option value="Hotel" selected>Hotel</option>
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label for="vendor">Vendor</label>
                      <input type="text" class="form-control" value="{{$hotel->vendor_name}}" required name="vendor[]" id="vendor" placeholder="Vendor Name" >
                    </div>
                    <div class="col-md-4">
                      <label for="nights" class="pull-left">Destination</label>
                      <input type="text" name="destination[]" value="{{$hotel->destination}}" class="form-control" required />
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-3">
                      <label for="nights" class="pull-left">Nights</label>
                      <input type="number"  name="nights[]" value="{{$hotel->nights}}" id="nights" onkeyup="calculateNightPrice()"  class="form-control" required />
                    </div>
                    <div class="col-md-3">
                      <label for="hotel_name" class="pull-left">Hotel Name</label>
                      <input type="text" id="hotel_name" value="{{$hotel->hotel_name}}" name="hotel_name[]" class="form-control" required />
                    </div>
                    <div class="col-md-3">
                      <label for="check_in_date" class="pull-left">Check-In Date</label>
                      <input type="date" id="check_in_date" value="{{$hotel->check_in}}" name="check_in[]"  id="datetimepicker4"class="form-control" required />
                    </div>
                    <div class="col-md-3">
                      <label for="check_out_date" class="pull-left">Check-Out Date</label>
                      <input type="date" id="check_out_date" name="check_out[]" value="{{$hotel->check_out}}"  id="datetimepicker4" value="" class="form-control" required />
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                      <label for="pax" class="pull-left">Pax</label>
                      <input type="number"  name="pax[]"  value="{{$hotel->pax}}" class="form-control" required />
                    </div>
                    <div class="col-md-4">
                      <label for="vendor_price" class="pull-left">Vendor Price</label>
                      <input type="number"  name="vendor_price[]" value="{{$hotel->vendor_price}}" id="vendor_price" onkeyup="calculateNightPrice()"  class="form-control"  required />
                    </div>
                    <div class="col-md-4">
                      <label for="our_price" class="pull-left">Our Price</label>
                      <input type="number"  name="our_price[]" value="{{$hotel->our_price}}" class="form-control" required />
                    </div>
                    <input type="hidden" name="add_on_service_price[]">
                    <input type="hidden" name="amount_paid_by_client[]">
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <label for="remarks">Remarks</label>
                      <textarea class="form-control" name="remarks[]" placeholder="Enter Remarks" id="remarks">{{$hotel->remarks}}</textarea>
                    </div>
                  </div>
                </div>
              </div>


              <div class="panel">
                <div class="panel-body">
                  @foreach($add_more as $am)
                    @if($am->service_type == 'Hotel')
                      <div class="card">
                        @if($convert)
                          <div class="card-header">
                            <div class="text-right">
                              <input type="checkbox" class="hotel" id="" > Hotel
                              <input type="hidden" name="boi_id[]" value="{{$am->id}}">
                            </div>
                          </div>
                        @endif
                        <div class="card-body">
                          <div class="row">
                            <input type="hidden" name="add_on[]" value="0">
                            <input type="hidden" name="add_more[]" value="1">
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
                              <input type="number"  name="nights[]" value="{{$am->nights}}" class="form-control" required />
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
                              <input type="number"  name="vendor_price[]" value="{{$am->vendor_price}}"  class="form-control" onkeyup="calculateVendorPrice()" required />
                            </div>
                            <div class="col-md-4">
                              <label for="our_price" class="pull-left">Our Price</label>
                              <input type="number"  name="our_price[]" value="{{$am->our_price}}"  class="form-control" required />
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-12">
                              <label for="remarks">Remarks</label>
                              <textarea class="form-control" name="remarks[]" placeholder="Enter Remarks" id="remarks">{{$am->remarks}}</textarea>
                            </div>
                          </div>
                          @if(!$convert)
                            <div class="row mt-2">
                              <div class="col-md-12">
                                <div class="text-right">
                                  <a href="javascript:void(0)" class="btn btn-danger" onclick="removeService(this);">Remove</a>
                                </div>
                              </div>
                            </div>
                          @endif
                        </div>
                      </div>
                    @elseif($am->service_type == 'Flight')
                      <div class="card">
                        @if($convert)
                          <div class="card-header">
                            <div class="text-right">
                              <input type="checkbox" class="flight" id="" > Flight
                              <input type="hidden" name="boi_id[]" value="{{$am->id}}">
                            </div>
                          </div>
                        @endif
                        <div class="card-body">
                          <div class="row">
                            <input type="hidden" name="add_on[]" value="0">
                            <input type="hidden" name="add_more[]" value="1">
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
                              <input type="number"  name="vendor_price[]" value="{{$am->vendor_price}}"  class="form-control" onkeyup="calculateVendorPrice()" required />
                            </div>
                            <div class="col-md-3">
                              <label for="our_price" class="pull-left">Our Price</label>
                              <input type="number"  name="our_price[]" value="{{$am->our_price}}"  class="form-control" required />
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-12">
                              <label for="remarks">Remarks</label>
                              <textarea class="form-control" name="remarks[]" placeholder="Enter Remarks" id="remarks">{{$am->remarks}}</textarea>
                            </div>
                          </div>
                          @if(!$convert)
                            <div class="row mt-2">
                              <div class="col-md-12">
                                <div class="text-right">
                                  <a href="javascript:void(0)" class="btn btn-danger" onclick="removeService(this);">Remove</a>
                                </div>
                              </div>
                            </div>
                          @endif
                        </div>
                      </div>
                    @elseif($am->service_type == 'Visa')
                      <div class="card">
                        @if($convert)
                          <div class="card-header">
                            <div class="text-right">
                              <input type="checkbox" class="visa" id=""> Visa
                              <input type="hidden" name="boi_id[]" value="{{$am->id}}">
                            </div>
                          </div>
                        @endif
                        <div class="card-body">
                          <div class="row">
                            <input type="hidden" name="add_on[]" value="0">
                            <input type="hidden" name="add_more[]" value="1">
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
                              <input type="number"  name="vendor_price[]" value="{{$am->vendor_price}}"  class="form-control" onkeyup="calculateVendorPrice()" required />
                            </div>
                            <div class="col-md-6">
                              <label for="our_price" class="pull-left">Our Price</label>
                              <input type="number"  name="our_price[]" value="{{$am->our_price}}"  class="form-control" required />
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-12">
                              <label for="remarks">Remarks</label>
                              <textarea class="form-control" name="remarks[]" placeholder="Enter Remarks" id="remarks">{{$am->remarks}}</textarea>
                            </div>
                          </div>
                          @if(!$convert)
                            <div class="row mt-2">
                              <div class="col-md-12">
                                <div class="text-right">
                                  <a href="javascript:void(0)" class="btn btn-danger" onclick="removeService(this);">Remove</a>
                                </div>
                              </div>
                            </div>
                          @endif
                        </div>
                      </div>
                    @elseif($am->service_type == 'Land Package/Transfer')
                      <div class="card">
                        @if($convert)
                          <div class="card-header">
                            <div class="text-right">
                              <input type="checkbox" class="land" id=""> Land Package/ Transfer
                              <input type="hidden" name="boi_id[]" value="{{$am->id}}">
                            </div>
                          </div>
                        @endif
                        <div class="card-body">
                          <div class="row">
                            <input type="hidden" name="add_on[]" value="0">
                            <input type="hidden" name="add_more[]" value="1">
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
                              <input type="number"  name="nights[]" value="{{$am->nights}}" class="form-control" required />
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
                              <input type="number"  name="vendor_price[]" value="{{$am->vendor_price}}"  class="form-control" onkeyup="calculateVendorPrice()" required />
                            </div>
                            <div class="col-md-4">
                              <label for="our_price" class="pull-left">Our Price</label>
                              <input type="number"  name="our_price[]" value="{{$am->our_price}}"  class="form-control" required />
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-12">
                              <label for="remarks">Remarks</label>
                              <textarea class="form-control" name="remarks[]" placeholder="Enter Remarks" id="remarks">{{$am->remarks}}</textarea>
                            </div>
                          </div>
                          @if(!$convert)
                            <div class="row mt-2">
                              <div class="col-md-12">
                                <div class="text-right">
                                  <a href="javascript:void(0)" class="btn btn-danger" onclick="removeService(this);">Remove</a>
                                </div>
                              </div>
                            </div>
                          @endif
                        </div>
                      </div>
                    @elseif($am->service_type == 'Insurance')
                      <div class="card">
                        @if($convert)
                          <div class="card-header">
                            <div class="text-right">
                              <input type="checkbox" class="insurance" id=""> Insurance
                              <input type="hidden" name="boi_id[]" value="{{$am->id}}">
                            </div>
                          </div>
                        @endif
                        <div class="card-body">
                          <div class="row">
                            <input type="hidden" name="add_on[]" value="0">
                            <input type="hidden" name="add_more[]" value="1">
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
                              <input type="number"  name="vendor_price[]" value="{{$am->vendor_price}}"  class="form-control" onkeyup="calculateVendorPrice()" required />
                            </div>
                            <div class="col-md-6">
                              <label for="our_price" class="pull-left">Our Price</label>
                              <input type="number"  name="our_price[]" value="{{$am->our_price}}"  class="form-control" required />
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-12">
                              <label for="remarks">Remarks</label>
                              <textarea class="form-control" name="remarks[]" placeholder="Enter Remarks" id="remarks">{{$am->remarks}}</textarea>
                            </div>
                          </div>
                          @if(!$convert)
                            <div class="row mt-2">
                              <div class="col-md-12">
                                <div class="text-right">
                                  <a href="javascript:void(0)" class="btn btn-danger" onclick="removeService(this);">Remove</a>
                                </div>
                              </div>
                            </div>
                          @endif
                        </div>
                      </div>
                    @endif
                  @endforeach
                  <div id="addMoreService"></div>
                </div>
                @if(!$convert)
                  <div class="col-md-12 mt-2 mb-2">
                    <div class="button-group text-center">
                      <a href="javascript:void(0)" class="btn btn-primary" onclick="addMoreService()" id="plus5">Add Service</a>
                    </div>
                  </div>
                @endif
              </div>

              @if(!$convert)
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
                          <div class="card-body">
                            <div class="row">
                              <input type="hidden" name="add_on[]" value="1">
                              <input type="hidden" name="add_more[]" value="0">
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
                            <div class="row">
                              <div class="col-md-12">
                                <label for="remarks">Remarks</label>
                                <textarea class="form-control" name="remarks[]" placeholder="Enter Remarks" id="remarks">{{$ao->remarks}}</textarea>
                              </div>
                            </div>
                            <div class="row mt-2">
                              <div class="col-md-12">
                                <div class="text-right">
                                  <a href="javascript:void(0)" class="btn btn-danger" onclick="removeService(this);">Remove</a>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      @elseif($ao->service_type == 'Flight')
                        <div class="card">
                          <div class="card-body">
                            <div class="row">
                              <input type="hidden" name="add_on[]" value="1">
                              <input type="hidden" name="add_more[]" value="0">
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
                            <div class="row">
                              <div class="col-md-12">
                                <label for="remarks">Remarks</label>
                                <textarea class="form-control" name="remarks[]" placeholder="Enter Remarks" id="remarks">{{$ao->remarks}}</textarea>
                              </div>
                            </div>
                            <div class="row mt-2">
                              <div class="col-md-12">
                                <div class="text-right">
                                  <a href="javascript:void(0)" class="btn btn-danger" onclick="removeService(this);">Remove</a>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      @elseif($ao->service_type == 'Visa')
                        <div class="card">
                          <div class="card-body">
                            <div class="row">
                              <input type="hidden" name="add_on[]" value="1">
                              <input type="hidden" name="add_more[]" value="0">
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
                            <div class="row">
                              <div class="col-md-12">
                                <label for="remarks">Remarks</label>
                                <textarea class="form-control" name="remarks[]" placeholder="Enter Remarks" id="remarks">{{$ao->remarks}}</textarea>
                              </div>
                            </div>
                            <div class="row mt-2">
                              <div class="col-md-12">
                                <div class="text-right">
                                  <a href="javascript:void(0)" class="btn btn-danger" onclick="removeService(this);">Remove</a>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      @elseif($ao->service_type == 'Land Package/Transfer')
                        <div class="card">
                          <div class="card-body">
                            <div class="row">
                              <input type="hidden" name="add_on[]" value="1">
                              <input type="hidden" name="add_more[]" value="0">
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
                            <div class="row">
                              <div class="col-md-12">
                                <label for="remarks">Remarks</label>
                                <textarea class="form-control" name="remarks[]" placeholder="Enter Remarks" id="remarks">{{$ao->remarks}}</textarea>
                              </div>
                            </div>
                            <div class="row mt-2">
                              <div class="col-md-12">
                                <div class="text-right">
                                  <a href="javascript:void(0)" class="btn btn-danger" onclick="removeService(this);">Remove</a>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      @elseif($ao->service_type == 'Insurance')
                        <div class="card">
                          <div class="card-body">
                            <div class="row">
                              <input type="hidden" name="add_on[]" value="1">
                              <input type="hidden" name="add_more[]" value="0">
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
                            <div class="row">
                              <div class="col-md-12">
                                <label for="remarks">Remarks</label>
                                <textarea class="form-control" name="remarks[]" placeholder="Enter Remarks" id="remarks">{{$ao->remarks}}</textarea>
                              </div>
                            </div>
                            <div class="row mt-2">
                              <div class="col-md-12">
                                <div class="text-right">
                                  <a href="javascript:void(0)" class="btn btn-danger" onclick="removeService(this);">Remove</a>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      @endif
                    @endforeach
                    <div id="addService"></div>
                  </div>
                  @if(!$convert)
                    <div class="col-md-12 mt-2 mb-2">
                      <div class="button-group text-center">
                        <a href="javascript:void(0)" class="btn btn-primary" onclick="addService()" id="plus5">Add Service</a>
                      </div>
                    </div>
                  @endif
                </div>
              @endif

              <hr>
              <div class="text-center">
                @if(!$convert)
                  <input type="submit" id="submit" class="btn btn-md btn-info" value="Update Offer Night Offer"/>
                @else
                  <input type="submit" id="submit" class="btn btn-md btn-info" style="display:none;"/>
                  <button type="button" onclick="removeUnchecked()" class="btn btn-md btn-info">Convert To Holiday</button>
                @endif
              </div>
            </div>
          </div>
        </form>

        <div class="col-md-12">

    </div>
  </div>


@endsection


@section('vendor-script')
  <!-- vendor files -->

@endsection
@section('page-script')

  <script>
    function removeUnchecked(){
      var hotel = $('input[type="checkbox"][class="hotel"]:checked');
      var flight =  $('input[type="checkbox"][class="flight"]:checked');
      var visa =  $('input[type="checkbox"][class="visa"]:checked');
      var insurance = $('input[type="checkbox"][class="insurance"]:checked');
      var land = $('input[type="checkbox"][class="land"]:checked');

      if(!hotel.length){
        swal({
          title: 'Oops!!',
          text: 'You Must Have A Hotel Service',
          icon: 'error'
        });
      }else{
        var buttons = $('input[type="checkbox"]:not(:checked)')
        for(var i= 0; i < buttons.length; i++){
          $(buttons[i]).parents('.card').remove();
        }
        swal({
          title: 'Converting!!....',
          text: 'Please Wait',
          icon: 'success'
        });
        $('#submit').click()
      }

    }
  </script>


  <script>
    window.onload = function(){
      @if($convert)
      $('input').attr('readonly','readonly');
      $('textarea').attr('readonly','readonly');
      @endif
    }
  </script>
  <script>
    function addService(){
      var service =
        '<div class="card">'+
        '<div class="card-body">'+
        '<div class="row">'+
        '<input type="hidden" name="add_on[]" value="1">'+
        '<input type="hidden" name="add_more[]" value="0">'+
        '<div class="col-md-4">'+
        '<label for="service_type">Service Type</label>'+
        '<select name="service_type[]" onchange="serviceType(this);" class="form-control" required>'+
        '<option value="">--Select Service Type--</option>'+
        '<option value="Hotel">Hotel</option>'+
        '<option value="Flight">Flight</option>'+
        '<option value="Visa">Visa</option>'+
        '<option value="Land Package/Transfer">Land Package/Transfer</option>'+
        '<option value="Insurance">Insurance</option>'+
        '</select>'+
        '</div>'+
        '<input type="hidden" name="vendor_price[]">'+
        '<input type="hidden" name="our_price[]">'+
        '<div class="col-md-4">'+
        '<label for="vendor">Vendor</label>'+
        '<input type="text" class="form-control" required name="vendor[]" id="vendor" placeholder="Vendor Name" >'+
        '</div>'+
        '<div class="col-md-4">'+
        '<label for="nights" class="pull-left">Destination</label>'+
        '<input type="text" name="destination[]" class="form-control" required />'+
        '</div>'+
        '</div>'+
        '<div class="serviceType"></div>'+
        '<div class="row">'+
        '<div class="col-md-12">'+
        '<label for="remarks">Remarks</label>'+
        '<textarea class="form-control" name="remarks[]" placeholder="Enter Remarks" id="remarks"></textarea>'+
        '</div>'+
        '</div>'+
        '<div class="row mt-2">'+
        '<div class="col-md-12">'+
        '<div class="text-right">'+
        '<a href="javascript:void(0)" class="btn btn-danger" onclick="removeService(this);">Remove</a>'+
        '</div>'+
        '</div>'+
        '</div>'+
        '</div>'+
        '</div>';
      $('#addService').append(service);
    }

    function removeService(foo){
      $(foo).parents('.card').remove();
    }
  </script>

  <script>
    function serviceType(foo){
      if(foo.value == 'Hotel'){
        var serviceType =
          '<div class="row">'+
          '<div class="col-md-3">'+
          '<label for="nights" class="pull-left">Nights</label>'+
          '<input type="number"  name="nights[]" class="form-control" required />'+
          '</div>'+
          '<div class="col-md-3">'+
          '<label for="hotel_name" class="pull-left">Hotel Name</label>'+
          '<input type="text" id="hotel_name" name="hotel_name[]" class="form-control" required />'+
          '</div>'+
          '<div class="col-md-3">'+
          '<label for="check_in_date" class="pull-left">Check-In Date</label>'+
          '<input type="date" id="check_in_date" name="check_in[]"  id="datetimepicker4"class="form-control" required />'+
          '</div>'+
          '<div class="col-md-3">'+
          '<label for="check_out_date" class="pull-left">Check-Out Date</label>'+
          '<input type="date" id="check_out_date" name="check_out[]"  id="datetimepicker4" value="" class="form-control" required />'+
          '</div>'+
          '</div>'+
          '<div class="row">'+
          '<div class="col-md-4">'+
          '<label for="pax" class="pull-left">Pax</label>'+
          '<input type="number"  name="pax[]"  class="form-control" required />'+
          '</div>'+
          '<div class="col-md-4">'+
          '<label for="add_on_service_price" class="pull-left">Add On Service Price</label>'+
          '<input type="number"  name="add_on_service_price[]" onkeyup="setMaxPrice(this);"  class="form-control" required />'+
          '</div>'+
          '<div class="col-md-4">'+
          '<label for="amount_paid_by_client" class="pull-left">Amount Paid By Client</label>'+
          '<input type="number"  name="amount_paid_by_client[]"  class="form-control amount-paid" required />'+
          '</div>'+
          '</div>';
      }else if(foo.value == 'Visa'){
        var serviceType =
          '<div class="row">'+
          '<div class="col-md-6">'+
          '<label for="add_on_service_price" class="pull-left">Add On Service Price</label>'+
          '<input type="number"  name="add_on_service_price[]" onkeyup="setMaxPrice(this);"  class="form-control" required />'+
          '</div>'+
          '<div class="col-md-6">'+
          '<label for="amount_paid_by_client" class="pull-left">Amount Paid By Client</label>'+
          '<input type="number"  name="amount_paid_by_client[]"  class="form-control amount-paid" required />'+
          '</div>'+
          '</div>';

      }else if(foo.value == 'Flight'){
        var serviceType =
          '<div class="row">'+
          '<div class="col-md-3">'+
          '<label for="flight_pax" class="pull-left">Pax</label>'+
          '<input type="number" name="flight_pax[]" class="form-control" required />'+
          '</div>'+
          '<div class="col-md-3">'+
          '<label for="flight_details" class="pull-left">Flight Details</label>'+
          '<input type="text" name="flight_details[]" class="form-control" required />'+
          '</div>'+
          '<div class="col-md-3">'+
          '<label for="add_on_service_price" class="pull-left">Add On Service Price</label>'+
          '<input type="number"  name="add_on_service_price[]" onkeyup="setMaxPrice(this);"  class="form-control" required />'+
          '</div>'+
          '<div class="col-md-3">'+
          '<label for="amount_paid_by_client" class="pull-left">Amount Paid By Client</label>'+
          '<input type="number"  name="amount_paid_by_client[]"  class="form-control amount-paid" required />'+
          '</div>'+
          '</div>';
      }else if(foo.value == 'Insurance'){
        var serviceType =
          '<div class="row">'+
          '<div class="col-md-6">'+
          '<label for="add_on_service_price" class="pull-left">Add On Service Price</label>'+
          '<input type="number"  name="add_on_service_price[]" onkeyup="setMaxPrice(this);"  class="form-control" required />'+
          '</div>'+
          '<div class="col-md-6">'+
          '<label for="amount_paid_by_client" class="pull-left">Amount Paid By Client</label>'+
          '<input type="number"  name="amount_paid_by_client[]"  class="form-control amount-paid" required />'+
          '</div>'+
          '</div>';
      }else if(foo.value == 'Land Package/Transfer'){
        var serviceType =
          '<div class="row">'+
          '<div class="col-md-3">'+
          '<label for="nights" class="pull-left">Nights</label>'+
          '<input type="number"  name="nights[]" class="form-control" required />'+
          '</div>'+
          '<div class="col-md-3">'+
          '<label for="hotel_name" class="pull-left">Hotel Name</label>'+
          '<input type="text" id="hotel_name" name="hotel_name[]" class="form-control" required />'+
          '</div>'+
          '<div class="col-md-3">'+
          '<label for="check_in_date" class="pull-left">Check-In Date</label>'+
          '<input type="date" id="check_in_date" name="check_in[]"  id="datetimepicker4"class="form-control" required />'+
          '</div>'+
          '<div class="col-md-3">'+
          '<label for="check_out_date" class="pull-left">Check-Out Date</label>'+
          '<input type="date" id="check_out_date" name="check_out[]"  id="datetimepicker4" value="" class="form-control" required />'+
          '</div>'+
          '</div>'+
          '<div class="row">'+
          '<div class="col-md-4">'+
          '<label for="pax" class="pull-left">Pax</label>'+
          '<input type="number"  name="pax[]"  class="form-control" required />'+
          '</div>'+
          '<div class="col-md-4">'+
          '<label for="add_on_service_price" class="pull-left">Add On Service Price</label>'+
          '<input type="number"  name="add_on_service_price[]" onkeyup="setMaxPrice(this);"  class="form-control" required />'+
          '</div>'+
          '<div class="col-md-4">'+
          '<label for="amount_paid_by_client" class="pull-left">Amount Paid By Client</label>'+
          '<input type="number"  name="amount_paid_by_client[]"  class="form-control amount-paid" required />'+
          '</div>'+
          '</div>';
      }
      $(foo).parents('.card').find('.serviceType').html(serviceType);
    }
  </script>

  {{-- <script>
      function setMaxPrice(foo){
          $(foo).parents('.card').find('.amount-paid').attr('max',foo.value);
      }
  </script> --}}


  {{-- <script>
      function calculateNightPrice(){
          var nights = document.getElementById('nights').value;
          var vendor_price = document.getElementById('vendor_price').value;
          @if($booking->client->offer_night_per_night_price)
              var val = {{$booking->client->offer_night_per_night_price}};
          @else
              var val = {{App\BookingLimitSettings::first()->offer_night_per_night_price}};
          @endif
          if(nights > 0){
              if( vendor_price > (nights * val) ){
                  $('#submit').attr('disabled','disabled');
                  $('#submit').val('Budget Exceeded');
                  swal({
                      title: 'Budget Exceeded',
                      text: 'Please Adjust Price',
                      icon: 'error'
                  });
              }else{
                  $('#submit').removeAttr('disabled');
                  $('#submit').val('Update Offer Night Offer');
              }
          }

      }
  </script> --}}


  <script>
    function addMoreService(){
      var service =
        '<div class="card">'+
        '<div class="card-body">'+
        '<div class="row">'+
        '<input type="hidden" name="add_on[]" value="0">'+
        '<input type="hidden" name="add_more[]" value="1">'+
        '<div class="col-md-4">'+
        '<label for="service_type">Service Type</label>'+
        '<select name="service_type[]" onchange="serviceMoreType(this);" class="form-control" required>'+
        '<option value="">--Select Service Type--</option>'+
        '<option value="Hotel">Hotel</option>'+
        '<option value="Flight">Flight</option>'+
        '<option value="Visa">Visa</option>'+
        '<option value="Land Package/Transfer">Land Package/Transfer</option>'+
        '<option value="Insurance">Insurance</option>'+
        '</select>'+
        '</div>'+
        '<input type="hidden"  name="add_on_service_price[]"/>'+
        '<input type="hidden"  name="amount_paid_by_client[]"/>'+
        '<div class="col-md-4">'+
        '<label for="vendor">Vendor</label>'+
        '<input type="text" class="form-control" required name="vendor[]" id="vendor" placeholder="Vendor Name" >'+
        '</div>'+
        '<div class="col-md-4">'+
        '<label for="nights" class="pull-left">Destination</label>'+
        '<input type="text" name="destination[]" class="form-control" required />'+
        '</div>'+
        '</div>'+
        '<div class="serviceMoreType"></div>'+
        '<div class="row">'+
        '<div class="col-md-12">'+
        '<label for="remarks">Remarks</label>'+
        '<textarea class="form-control" name="remarks[]" placeholder="Enter Remarks" id="remarks"></textarea>'+
        '</div>'+
        '</div>'+
        '<div class="row mt-2">'+
        '<div class="col-md-12">'+
        '<div class="text-right">'+
        '<a href="javascript:void(0)" class="btn btn-danger" onclick="removeService(this);">Remove</a>'+
        '</div>'+
        '</div>'+
        '</div>'+
        '</div>'+
        '</div>';
      $('#addMoreService').append(service);
    }

    function removeService(foo){
      $(foo).parents('.card').remove();
    }
  </script>

  <script>
    function serviceMoreType(foo){
      if(foo.value == 'Hotel'){
        var serviceType =
          '<div class="row">'+
          '<div class="col-md-3">'+
          '<label for="nights" class="pull-left">Nights</label>'+
          '<input type="number"  name="nights[]" class="form-control" required />'+
          '</div>'+
          '<div class="col-md-3">'+
          '<label for="hotel_name" class="pull-left">Hotel Name</label>'+
          '<input type="text" id="hotel_name" name="hotel_name[]" class="form-control" required />'+
          '</div>'+
          '<div class="col-md-3">'+
          '<label for="check_in_date" class="pull-left">Check-In Date</label>'+
          '<input type="date" id="check_in_date" name="check_in[]"  id="datetimepicker4"class="form-control" required />'+
          '</div>'+
          '<div class="col-md-3">'+
          '<label for="check_out_date" class="pull-left">Check-Out Date</label>'+
          '<input type="date" id="check_out_date" name="check_out[]"  id="datetimepicker4" value="" class="form-control" required />'+
          '</div>'+
          '</div>'+
          '<div class="row">'+
          '<div class="col-md-4">'+
          '<label for="pax" class="pull-left">Pax</label>'+
          '<input type="number"  name="pax[]"  class="form-control" required />'+
          '</div>'+
          '<div class="col-md-4">'+
          '<label for="vendor_price" class="pull-left">Vendor Price</label>'+
          '<input type="number"  name="vendor_price[]"  class="form-control" onkeyup="calculateVendorPrice()" required />'+
          '</div>'+
          '<div class="col-md-4">'+
          '<label for="our_price" class="pull-left">Our Price</label>'+
          '<input type="number"  name="our_price[]"  class="form-control" required />'+
          '</div>'+
          '</div>';
      }else if(foo.value == 'Visa'){
        var serviceType =
          '<div class="row">'+
          '<div class="col-md-6">'+
          '<label for="vendor_price" class="pull-left">Vendor Price</label>'+
          '<input type="number"  name="vendor_price[]"  class="form-control" onkeyup="calculateVendorPrice()" required />'+
          '</div>'+
          '<div class="col-md-6">'+
          '<label for="our_price" class="pull-left">Our Price</label>'+
          '<input type="number"  name="our_price[]"  class="form-control" required />'+
          '</div>'+
          '</div>';

      }else if(foo.value == 'Flight'){
        var serviceType =
          '<div class="row">'+
          '<div class="col-md-3">'+
          '<label for="flight_pax" class="pull-left">Pax</label>'+
          '<input type="number" name="flight_pax[]" class="form-control" required />'+
          '</div>'+
          '<div class="col-md-3">'+
          '<label for="flight_details" class="pull-left">Flight Details</label>'+
          '<input type="text" name="flight_details[]" class="form-control" required />'+
          '</div>'+
          '<div class="col-md-3">'+
          '<label for="vendor_price" class="pull-left">Vendor Price</label>'+
          '<input type="number"  name="vendor_price[]"  class="form-control" onkeyup="calculateVendorPrice()" required />'+
          '</div>'+
          '<div class="col-md-3">'+
          '<label for="our_price" class="pull-left">Our Price</label>'+
          '<input type="number"  name="our_price[]"  class="form-control" required />'+
          '</div>'+
          '</div>';
      }else if(foo.value == 'Insurance'){
        var serviceType =
          '<div class="row">'+
          '<div class="col-md-6">'+
          '<label for="vendor_price" class="pull-left">Vendor Price</label>'+
          '<input type="number"  name="vendor_price[]"  class="form-control" onkeyup="calculateVendorPrice()" required />'+
          '</div>'+
          '<div class="col-md-6">'+
          '<label for="our_price" class="pull-left">Our Price</label>'+
          '<input type="number"  name="our_price[]"  class="form-control" required />'+
          '</div>'+
          '</div>';
      }else if(foo.value == 'Land Package/Transfer'){
        var serviceType =
          '<div class="row">'+
          '<div class="col-md-3">'+
          '<label for="nights" class="pull-left">Nights</label>'+
          '<input type="number"  name="nights[]" class="form-control" required />'+
          '</div>'+
          '<div class="col-md-3">'+
          '<label for="hotel_name" class="pull-left">Hotel Name</label>'+
          '<input type="text" id="hotel_name" name="hotel_name[]" class="form-control" required />'+
          '</div>'+
          '<div class="col-md-3">'+
          '<label for="check_in_date" class="pull-left">Check-In Date</label>'+
          '<input type="date" id="check_in_date" name="check_in[]"  id="datetimepicker4"class="form-control" required />'+
          '</div>'+
          '<div class="col-md-3">'+
          '<label for="check_out_date" class="pull-left">Check-Out Date</label>'+
          '<input type="date" id="check_out_date" name="check_out[]"  id="datetimepicker4" value="" class="form-control" required />'+
          '</div>'+
          '</div>'+
          '<div class="row">'+
          '<div class="col-md-4">'+
          '<label for="pax" class="pull-left">Pax</label>'+
          '<input type="number"  name="pax[]"  class="form-control" required />'+
          '</div>'+
          '<div class="col-md-4">'+
          '<label for="vendor_price" class="pull-left">Vendor Price</label>'+
          '<input type="number"  name="vendor_price[]"  class="form-control" onkeyup="calculateVendorPrice()" required />'+
          '</div>'+
          '<div class="col-md-4">'+
          '<label for="our_price" class="pull-left">Our Price</label>'+
          '<input type="number"  name="our_price[]"  class="form-control" required />'+
          '</div>'+
          '</div>';
      }
      $(foo).parents('.card').find('.serviceMoreType').html(serviceType);
    }
  </script>
@endsection

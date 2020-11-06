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
    <div class="col-md-12">
      <div class="card">
        <form action="{{ route('update.client.booking',['id'=>$booking->id]) }}" method="post">
          @csrf
          <div class="card-body">
            <div class="row">
              <div class="col-md-3">
                <label for="">Name</label>
                <input type="text" id="client_name" name="name" value="{{$booking->client->name}}" class="form-control" readonly>
              </div>
              <div class="col-md-3">
                <label for="Destination">Date of Enrollment</label>
                <input type="text" id="date_of_enrollment" value="{{$booking->client->latestPackage->enrollmentDate}}" name="date_of_enrollment" class="form-control" readonly>
              </div>
              <div class="col-md-3">
                <label for="Destination">MAF No.</label>
                <input type="text" name="maf_no" id="maf_no" value="{{$booking->client->latestPackage->mafNo}}" class="form-control" readonly>
              </div>
              <div class="col-md-3">
                <label for="Destination">Mobile No.</label>
                <input type="text" id="mobile_no" name="mobile_no" value="{{$booking->client->phone}}" class="form-control" readonly>
              </div>
            </div>
            <div class="row">
              <div class="col-md-3">
                <label for="booking_request_date">Booking Request On</label>
                <input type="date" required name="booking_request_date" class="form-control" value="{{ $booking->bookingRequestDate }}">
              </div>
              <div class="col-md-3">
                <label for="travel_date">Travel Date</label>
                <input type="date" required  name="travel_date" class="form-control" id="travel_date" value="{{ $booking->travelDate }}">
              </div>
              <div class="col-md-3">
                <label for="nights">No. of Nights</label>
                <input type="number" required name="total_nights" id="total_nights" class="form-control " value="{{ $booking->totalNights }}">
              </div>

              <div class="col-md-3">
                <label for="holiday_type">Holiday Type</label>
                <select name="holiday_type" required id="" class="form-control">
                  <option value="">--SELECT--</option>
                  <option value="Fully Paid Holiday" {{ $booking->holidayType == 'Fully Paid Holiday' ? 'selected' : '' }}>Fully Paid Holiday</option>
                  <option value="Stay Only Holiday" {{ $booking->holidayType == 'Stay Only Holiday' ? 'selected' : '' }}>Stay Only Holiday</option>
                  <option value="Adjustment" {{ $booking->holidayType == 'Adjustment' ? 'selected' : '' }}>Adjustment</option>
                  <option value="Flight Only" {{ $booking->holidayType == 'Flight Only' ? 'selected' : '' }}>Flight Only</option>
                  <option value="Offer Nights" {{ $booking->holidayType == 'Offer Nights' ? 'selected' : '' }}>Offer Nights</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col-md-3">
                <label for="breakfast">Breakfast</label><br>
                <input type="radio" name="breakfast" value="1" id="" required {{ $booking->breakfast == '1' ? 'checked' : '' }}>Yes
                <input type="radio" name="breakfast"  value="0" id="" required {{ $booking->breakfast ==  '0' ? 'checked' : '' }}>No
              </div>
              <div class="col-md-3">
                <label for="">Add More</label><br>
                <button type="button" onclick="addMore();" class="btn btn-warning">+</button>
              </div>
            </div>
          @foreach($booking->BookingInfo as $info)
            <div class="remove">
            <hr>
            <div class="row">
              <div class="col-md-3">
                <label for="Destination">Destination</label>
                <input type="text" name="destination[]" required class="form-control" value="{{ $info->destination }}">
              </div>
              <div class="col-md-3">
                <label for="Destination">Nights</label>
                <input type="number" name="nights[]" required class="form-control" value="{{ $info->nights }}">
              </div>
              <div class="col-md-3">
                <label for="adults">Adults</label>
                <input type="number" name="adults[]" required class="form-control" value="{{ $info->adults }}">
              </div>
              <div class="col-md-3">
                <label for="kids">Kids</label>
                <input type="number" name="kids[]" class="form-control" required value="{{ $info->kids }}">
              </div>
            </div>
            <div class="row">
              <div class="col-md-4">
                <label for="check_in">Check In</label>
                <input type="date" name="check_in[]" id="check_in" required class="form-control check_in" value="{{ $info->check_in }}">
              </div>
              <div class="col-md-4">
                <label for="check_out">Check Out</label>
                <input type="date" name="check_out[]" id="check_out" required class="form-control check_out" value="{{ $info->check_out }}">
              </div>
              <div class="col-md-3">
                <label for="">Remove</label><br>
                <button type="button" onclick="remove(this);" class="btn btn-danger btn-sm">Remove</button>
              </div>
            </div>
          </div>
          @endforeach
            <div id="append"></div>

            <div class="row">
              <div class="col-md-12"><hr>
                <label for="Destination">Remarks</label>
                <textarea name="remarks" class="form-control" required placeholder="Kindly Mention the mode of booking request received">{{ $booking->remarks }}</textarea>
              </div>
            </div>
          </div>
          <div class="text-center">
            <button type="submit" class="btn btn-md btn-success">Update Booking Request</button>
          </div>
        </form>
      </div>
    </div>
  </div>


@endsection


@section('vendor-script')
  <!-- vendor files -->

@endsection
@section('page-script')
  <script>
    $( "#travel_date" ).on( "change", function() {
      dateChange();
    });

    $( ".check_in" ).on( "change", function() {
      dateChange();
    });

    $( "#total_nights" ).on( "keyup", function() {
      dateChange();
    });

    function dateChange(){
      var travel_date = $('#travel_date').val();
      var old_date = new Date(travel_date);
      var nextday = new Date(old_date.getFullYear(),old_date.getMonth(),old_date.getDate()+1);


      if(Number(nextday.getDate()) <= 9){
        final_day = 0+(nextday.getDate()).toString();
      }else{
        final_day = nextday.getDate();
      }

      if(Number(nextday.getMonth())+1 <= 9){
        final_month = 0+(Number(nextday.getMonth())+1).toString();
      }else{
        final_month = Number(nextday.getMonth())+1;
      }

      final_year = nextday.getFullYear();

      var final_date = final_year + '-' + final_month + '-' + final_day;
      $('#check_in').attr('max',final_date);
      $('#check_in').attr('min',travel_date);

      //


      var nights = $('#total_nights').val();
      var check_in = $('#check_in').val();
      var old_date = new Date(check_in);
      var new_date = new Date(old_date.getFullYear(),old_date.getMonth(),old_date.getDate()+Number(nights));

      if(Number(new_date.getDate()) <= 9){
        final_day = 0+(new_date.getDate()).toString();
      }else{
        final_day = new_date.getDate();
      }

      if(Number(new_date.getMonth())+1 <= 9){
        final_month = 0+(Number(new_date.getMonth())+1).toString();
      }else{
        final_month = Number(new_date.getMonth())+1;
      }

      final_year = new_date.getFullYear();
      var final_date = final_year + '-' + final_month + '-' + final_day;
      $('#check_out').attr('max',final_date);
      $('#check_out').attr('min',check_in);
    }

  </script>



  <script>
    function swalIt(){
      swal({
        title: 'Booking Already Exist',
        text: 'Please Process Previous Booking First To Add A New Booking!!',
        icon: 'error'
      });
    }
  </script>
  <script>
    function addMore(){
      var data = '<div class="remove"><hr><div class="row">'+
        '                        <div class="col-md-3">'+
        '<label for="Destination">Destination</label>'+
        '<input type="text" name="destination[]" required class="form-control">'+
        '                        </div>'+
        '                        <div class="col-md-3">'+
        '<label for="nights">Nights</label>'+
        '<input type="number" name="nights[]" required class="form-control">'+
        '                        </div>'+
        '                        <div class="col-md-3">'+
        '<label for="adults">Adults</label>'+
        '<input type="number" name="adults[]" required class="form-control">'+
        '                        </div>'+
        '                        <div class="col-md-3">'+
        '<label for="kids">Kids</label>'+
        '<input type="number" required name="kids[]" class="form-control">'+
        '                        </div>'+
        '</div>'+
        '<div class="row">'+
        '                        <div class="col-md-4">'+
        '<label for="check_in">Check In</label>'+
        '<input type="date" name="check_in[]" required class="form-control check_in">'+
        '                        </div>'+
        '                        <div class="col-md-4">'+
        '<label for="check_out">Check Out</label>'+
        '<input type="date" name="check_out[]" required class="form-control check_out">'+
        '                        </div>'+
        '                        <div class="col-md-3">'+
        '<label for="">&nbsp;</label><br>'+
        '<button type="button" onclick="remove(this);" class="btn btn-danger btn-sm">Remove</button>'+
        '                        </div>'+
        '</div></div>';

      $('#append').append(data);
    }

    function remove(foo){
      $(foo).parents(".remove").remove();
    }
  </script>

@endsection

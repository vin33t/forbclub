@extends('layouts/contentLayoutMaster')

@section('title', 'Booking Requests')

@section('vendor-style')
  {{-- vendor css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
@endsection
@section('content')


  <section id="basic-tabs-components">
    <div class="row">

      @foreach(\App\Client\Booking\Bookings::all() as $booking)
      <div class="col-md-6">
        <div class="card card-box">
          <div class="card-head">
            <div class="row">
              <div class="col-md-4 pull-left">
                <a href="{{ route('view.client',['slug'=>$booking->client->slug]) }}" target="_blank"><i class="fa fa-2x fa-user-circle" aria-hidden="true"></i></a>
              </div>
              <div class="col-md-4 center"><h4 class="font-weight-bold"><b>{{ $booking->client->name }}</b></h4></div>

            </div>
            <div class="row">
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
                <div class="col-md-4">Pax <br> <b>{{ $info->adults }} Adults <br>   </b>
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
  </section>


@endsection



@section('vendor-script')
  {{-- vendor files --}}
  <script src="{{ asset(mix('vendors/js/tables/datatable/pdfmake.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/vfs_fonts.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.html5.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.print.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
@endsection
@section('page-script')
  {{-- Page js files --}}
  <script src="{{ asset(mix('js/scripts/datatables/datatable.js')) }}"></script>
  <script src="{{ asset(mix('js/scripts/modal/components-modal.js')) }}"></script>

@endsection

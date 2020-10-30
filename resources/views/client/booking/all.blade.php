
@extends('layouts/contentLayoutMaster')

@section('title', 'Booking Requests')

@section('vendor-style')
  {{-- vendor css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
@endsection

@section('content')

  <!-- Zero configuration table -->
  <section id="basic-datatable">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Booking Requests</h4>
          </div>
          <div class="card-content">
            <div class="card-body card-dashboard">
              <div class="table-responsive">
                <table class="table zero-configuration">
                  <thead>
                  <tr>
                    <th>#</th>
                    <th>Added On</th>
                    <th>Booking Request Date</th>
                    <th>Client Name</th>
                    <th>Booking Status</th>
                    <th>Requirement</th>
                    <th>Added By</th>
                    <th>Offer Status</th>
                    <th>Offer Approved/Rejected By</th>
                    <th>Amount</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($bookings as $booking)
                    <tr>
                      <td>{{ $loop->index + 1 }}</td>
                      <td>{{ $booking->created_at }}</td>
                      <td>{{ $booking->bookingRequestDate }}</td>
                      <td><a href="{{ route('view.client',['slug'=>$booking->client->slug]) }}">{{ $booking->Client->name }}</a></td>
                      <td>{{ strtoupper($booking->status) }}</td>
                      <td>{{ strtoupper($booking->holidayType) }}</td>
                      <td>{{ App\User::find($booking->addedBy)->name }}</td>
                      <td>{{ $booking->offerStatus != NULL ? strtoupper($booking->offerStatus) : 'Offer Not Sent Yet' }}</td>
                      <td>{{ $booking->offerStatusUpdatedBy != NULL ? App\User::find($booking->offerStatusUpdatedBy)->name : 'Not Updated' }}</td>
                      <td>@if(!$booking->ClientHoliday) @if($booking->BookingOffer){{ '₹ ' . IND_money_format($booking->BookingOffer->BookingOfferInfo->pluck('our_price')->sum()) }} @else {{ 'Offer Not Sent Yet' }}@endif  @else
                          @php
                            $total = 0;
                                foreach($booking->ClientHoliday->ClientHolidayDetails as $details) {
                                    $total = $details->ClientHolidayTransactions->pluck('amount')->sum();
                            }
                          @endphp {{ '₹ ' . IND_money_format($total) }} @endif</td>
                      {{-- <td>{{ \Carbon\Carbon::parse($booking->created_at)->toDayDateTimeString() }} <br> ({{ $booking->created_at->diffForHumans() }})</td> --}}
                      <td>
                    </tr>
                  @endforeach
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>#</th>
                    <th>Added On</th>
                    <th>Booking Request Date</th>
                    <th>Client Name</th>
                    <th>Status</th>
                    <th>Requirement</th>
                    <th>Added By</th>
                    <th>Status</th>
                    <th>Approved/Rejected By</th>
                    <th>Amount</th>
                  </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
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
@endsection

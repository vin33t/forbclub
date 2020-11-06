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
            <h4 class="card-title">Booking Requests <button class="btn btn-primary btn-sm" data-toggle="modal" id="addRequestButton" data-target="#add_request"><i class="fa fa-plus-square"></i></button></h4>
            <div class="modal fade" id="add_request" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Search Client</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body blockThis">
                    <div class="row">
                      <div class="col-md-12">
                        <label for="search">Client Name/FTK/Phone</label>
                        <input type="search" name="q" class="form-control search-input" placeholder="Search" id="search" autocomplete="off">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
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
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($bookings as $booking)
                    <tr>
                      <td>{{ $loop->index + 1 }}</td>
                      <td>{{ $booking->created_at }}</td>
                      <td>{{ $booking->bookingRequestDate }}</td>
                      <td><a
                          href="{{ route('view.client',['slug'=>$booking->client->slug]) }}">{{ $booking->Client->name }}</a>
                      </td>
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
                        <button class="btn btn-primary btn-sm" data-toggle="modal"
                                data-target="#bookingDetailsView{{$booking->id}}">View
                        </button>
                        <div class="modal fade" id="bookingDetailsView{{$booking->id}}" tabindex="-1" role="dialog"
                             aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">{{ $booking->client->name }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
                                @foreach($booking->BookingInfo as $info)
                                <div class="row">
                                  <div class="col-md-2"></div>
                                  <div class="col-md-4"><strong>Destination:</strong></div>
                                  <div class="col-md-4">{{ $info->destination }}</div>
                                </div>
                                <div class="row">
                                  <div class="col-md-2"></div>
                                  <div class="col-md-4"><strong>Nights:</strong></div>
                                  <div class="col-md-4">{{ $info->nights }}</div>
                                </div>
                                <div class="row">
                                  <div class="col-md-2"></div>
                                  <div class="col-md-4"><strong>Pax:</strong></div>
                                  <div class="col-md-4">{{ $info->adults }} Adults ,{{$info->kids}} Kids</div>
                                </div>
                                <div class="row">
                                  <div class="col-md-2"></div>
                                  <div class="col-md-4"><strong>Breakfast:</strong></div>
                                  <div class="col-md-4">{{ $booking->breakfast ? 'Yes' : 'No' }}</div>
                                </div>
                                <div class="row">
                                  <div class="col-md-2"></div>
                                  <div class="col-md-4"><strong>Eligible:</strong></div>
                                  <div class="col-md-4">{{ $booking->eligible ? 'Yes' : 'No' }}</div>
                                </div>
                                <div class="row">
                                  <div class="col-md-2"></div>
                                  <div class="col-md-4"><strong>Check In::</strong></div>
                                  <div class="col-md-4">{{ $info->check_in }}</div>
                                </div>
                                <div class="row">
                                  <div class="col-md-2"></div>
                                  <div class="col-md-4"><strong>Check out:</strong></div>
                                  <div class="col-md-4">{{ $info->check_in }}</div>
                                </div>
                                <div class="row">
                                  <div class="col-md-2"></div>
                                  <div class="col-md-4"><strong>Remarks:</strong></div>
                                  <div class="col-md-4">{{ $booking->remarks }}</div>
                                </div>

                                @endforeach
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </td>
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
  <script>
    jQuery(document).ready(function($) {

      // Set the Options for "Bloodhound" suggestion engine
      var engine = new Bloodhound({
        remote: {
          url: '/find?q=%QUERY%',
          wildcard: '%QUERY%'
        },
        datumTokenizer: Bloodhound.tokenizers.whitespace('q'),
        queryTokenizer: Bloodhound.tokenizers.whitespace
      });
      $(".search-input").typeahead({
        hint: true,
        highlight: true,
        autocomplete: true,
        minLength: 2,
        valueKey: 'name'
      }, {
        source: engine.ttAdapter(),

        // This will be appended to "tt-dataset-" to form the class name of the suggestion menu.
        name: 'clientList',

        // the key from the array we want to display (name,id,email,etc...)
        templates: {
          empty: [
            '<div class="list-group search-results-dropdown"><div class="list-group-item">No Client found.</div></div>'
          ],
          header: [
            '<div class="list-group search-results-dropdown">'
          ],
          suggestion: function (data) {
            return '<a href="/booking/create/' +  data.slug + '" class="list-group-item" onclick="block()">' + data.name + ' - @' + data.phone + ' - ' + data.email +'</a>'
          }
        }
      });
    });
  </script>

@endsection

<nav class="navbar navbar-expand-lg">


    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item active">
          @if(Route::currentRouteName() == 'booking')
            <button class="btn btn-primary btn-sm">
              <a class="nav-link white" href="{{ route('booking') }}">New Bookings
                ({{ \App\Client\Booking\Bookings::where('status',NULL)->get()->count() }})</a>
            </button>
          @else
            <a class="nav-link" href="{{ route('booking') }}">New </a>
          @endif
        </li>
        <li class="nav-item">
          @if(Route::currentRouteName() == 'booking.processing.mrd')
            <button class="btn btn-primary btn-sm">
              <a class="nav-link white" href="{{ route('booking.processing.mrd') }}">In Processing By MRD
                ({{ \App\Client\Booking\Bookings::where('status','approved')->where('offerStatus',NULL)->get()->count() }})</a>
            </button>
          @else
            <a class="nav-link" href="{{ route('booking.processing.mrd') }}">In Processing By MRD
              ({{ \App\Client\Booking\Bookings::where('status','approved')->where('offerStatus',NULL)->get()->count() }})</a>
          @endif
        </li>
        <li class="nav-item">
          @if(Route::currentRouteName() == 'booking.manager.approved')
            <button class="btn btn-primary btn-sm">
              <a class="nav-link white" href="{{ route('booking.manager.approved') }}">Approved By Manager
                 ({{ \App\Client\Booking\Bookings::where('status','approved')->where('offerStatus','approved')->doesntHave('ClientHoliday')->get()->count() }})</a>
            </button>
          @else
            <a class="nav-link" href="{{ route('booking.manager.approved') }}">Approved By Manager
               ({{ \App\Client\Booking\Bookings::where('status','approved')->where('offerStatus','approved')->doesntHave('ClientHoliday')->get()->count() }})</a>
          @endif
        </li>
        <li class="nav-item">
          @if(Route::currentRouteName() == 'booking.holiday.progress')
            <button class="btn btn-primary btn-sm">
              <a class="nav-link white" href="{{ route('booking.holiday.progress') }}">Holiday In Progress   ({{ \App\Client\Booking\Bookings::where('status','approved')->where('offerStatus','approved')->whereHas('ClientHoliday')->get()->count() }})</a>
            </button>
          @else
            <a class="nav-link" href="{{ route('booking.holiday.progress') }}">Holiday In Progress   ({{ \App\Client\Booking\Bookings::where('status','approved')->where('offerStatus','approved')->whereHas('ClientHoliday')->get()->count() }})</a>
          @endif
        </li>
        <li class="nav-item">
          <button class="btn btn-primary btn-sm" data-toggle="modal" id="addRequestButton" data-target="#add_request"><i class="fa fa-plus-square"></i></button>
          <a href="{{ route('booking.denied.mrd') }}"><button class="btn btn-danger btn-sm" >Closed Bookings</button></a>
          <a href="{{ route('booking.all') }}"><button class="btn btn-primary btn-sm" >Booking Summary</button></a>

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

        </li>

      </ul>
    </div>
</nav>
<hr>

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

      </ul>
    </div>
</nav>
<hr>

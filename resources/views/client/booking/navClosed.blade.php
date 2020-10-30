<nav class="navbar navbar-expand-lg">



    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item active">
            @if(Route::currentRouteName() == 'booking.denied.mrd')
              <button class="btn btn-primary btn-sm">
                <a class="nav-link white" href="{{ route('booking.denied.mrd') }}">Denied By MRD
                  ({{ \App\Client\Booking\Bookings::where('status','rejected')->get()->count() }})</a>
              </button>
            @else
              <a class="nav-link" href="{{ route('booking.denied.mrd') }}">Denied By MRD ({{ \App\Client\Booking\Bookings::where('status','rejected')->get()->count() }}) </a>
            @endif
          </li>
          <li class="nav-item">
            @if(Route::currentRouteName() == 'booking.denied.manager')
              <button class="btn btn-primary btn-sm">
                <a class="nav-link white" href="{{ route('booking.denied.manager') }}">Denied By Manager
                  ({{ \App\Client\Booking\Bookings::where('offerStatus','rejected')->get()->count() }})</a>
              </button>
            @else
              <a class="nav-link" href="{{ route('booking.denied.manager') }}">Denied By Manager
                ({{ \App\Client\Booking\Bookings::where('offerStatus','rejected')->get()->count() }})</a>
            @endif
          </li>
          <li class="nav-item">
            @if(Route::currentRouteName() == 'booking.converted')
              <button class="btn btn-primary btn-sm">
                <a class="nav-link white" href="{{ route('booking.converted') }}">Converted to Holiday
                  ({{ \App\Client\Booking\Bookings::whereHas('ClientHoliday')->get()->count() }})</a>
              </button>
            @else
              <a class="nav-link" href="{{ route('booking.converted') }}">Converted to Holiday
                ({{ \App\Client\Booking\Bookings::whereHas('ClientHoliday')->get()->count() }})</a>
            @endif
          </li>
{{--          <li class="nav-item">--}}
{{--            @if(Route::currentRouteName() == 'booking.holiday.progress')--}}
{{--              <button class="btn btn-primary btn-sm">--}}
{{--                <a class="nav-link white" href="{{ route('booking.holiday.progress') }}">Cancelled Requests   </a>--}}
{{--              </button>--}}
{{--            @else--}}
{{--              <a class="nav-link" href="{{ route('booking.holiday.progress') }}">Cancelled Holiday</a>--}}
{{--            @endif--}}
{{--          </li>--}}

        </ul>
      </div>

</nav>
<hr>

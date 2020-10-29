<nav class="navbar navbar-expand-lg">
  <button class="btn btn-primary btn-sm">
    @if(Route::currentRouteName() == 'booking.denied.mrd')
      Denied By MRD ({{ \App\Client\Booking\Bookings::where('status','rejected')->get()->count() }})
    @elseif(Route::currentRouteName() == 'booking.denied.manager')
      Denied By Manager ({{ $bookings->count() }})
    @elseif(Route::currentRouteName() == 'booking.manager.approved')
      Approved By Manager ({{ $bookings->count() }})
    @elseif(Route::currentRouteName() == 'booking.converted')
      Converted to Holiday{{ $bookings->count() }})
    @endif


    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item active">
          <a class="nav-link" href="{{ route('booking.denied.mrd') }}">Denied By MRD </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('booking.denied.manager') }}">Denied By Manager</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('booking.converted') }}">Converted to Holiday</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('booking.holiday.progress') }}">Cancelled Requests</a>
        </li>

      </ul>
    </div>
</nav>
<hr>

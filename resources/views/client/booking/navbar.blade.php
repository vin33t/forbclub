<nav class="navbar navbar-expand-lg">
  <button class="btn btn-primary btn-sm">
  @if(Route::currentRouteName() == 'booking')
    New Bookings ({{ \App\Client\Booking\Bookings::where('status',NULL)->get()->count() }})
  @elseif(Route::currentRouteName() == 'booking.processing.mrd')
    In Processing By MRD  ({{ $bookings->count() }})
    @elseif(Route::currentRouteName() == 'booking.manager.approved')
   Approved By Manager ({{ $bookings->count() }})
    @elseif(Route::currentRouteName() == 'booking.holiday.progress')
   Holiday In Progress ({{ $bookings->count() }})
  @endif


  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item active">
        <a class="nav-link" href="{{ route('booking') }}">New </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('booking.processing.mrd') }}">In Processing By MRD</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('booking.manager.approved') }}">Approved By Manager</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('booking.holiday.progress') }}">Holiday In Progress</a>
      </li>

    </ul>
  </div>
</nav>
<hr>

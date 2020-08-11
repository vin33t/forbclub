@extends('layouts/fullLayoutMaster')

@section('title', 'Maintenance')

@section('content')
  <!-- maintenance -->
  <section class="row flexbox-container">
    <div class="col-xl-7 col-md-8 col-12 d-flex justify-content-center">
      <div class="card auth-card bg-transparent shadow-none rounded-0 mb-0 w-100">
        <div class="card-content">
          <div class="card-body text-center">
            <img src="{{ asset('images/pages/maintenance-2.png') }}" class="img-fluid align-self-center" alt="branding logo">
            <h1 class="font-large-2 my-1">Under Maintenance!</h1>
            <p class="px-2">
              @if(json_decode(file_get_contents(storage_path('framework/down')), true)['message'] )
              Sorry for inconvenience. We are <strong>"{{ json_decode(file_get_contents(storage_path('framework/down')), true)['message'] }}"</strong>. <br>
              Please try again in
                @if(json_decode(file_get_contents(storage_path('framework/down')), true)['retry'] )
                </strong>{{ json_decode(file_get_contents(storage_path('framework/down')), true)['retry'] }}</strong>
                @else
                  few
                @endif
                minutes
              @else
                Please try again later.
                @endif
            </p>
            <a class="btn btn-primary btn-lg mt-1" href="{{ url()->previous() }}">Back to Home</a>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- maintenance end -->
@endsection

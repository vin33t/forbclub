@extends('layouts/contentLayoutMaster')

@section('title', 'Dashboard Analytics')

@section('vendor-style')
  <!-- vendor css files -->
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/tether-theme-arrows.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/tether.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/shepherd-theme-default.css')) }}">
@endsection
@section('page-style')
  <!-- Page css files -->
  <link rel="stylesheet" href="{{ asset(mix('css/pages/dashboard-analytics.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/pages/card-analytics.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/plugins/tour/tour.css')) }}">
@endsection

@section('content')
  {{-- Dashboard Analytics Start --}}
  <section id="dashboard-analytics">
    @if(!request()->type)
    <div class="row">
      <div class="col-lg-3 col-md-6 col-12">
        <div class="card">
          <div class="card-header d-flex flex-column align-items-start pb-0">
            <div class="avatar bg-rgba-warning p-50 m-0">
              <div class="avatar-content">
                <i class="feather icon-user text-danger font-medium-5"></i>
              </div>
            </div>
            <a href="{{ route('dashboard',['type'=>'clients']) }}">
              <h2 class="text-bold-700 mt-1 mb-25">{{ \App\Client\Client::all()->count() }}</h2>
              <p class="mb-0">{{ __('Clients') }}</p>

            </a>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-12">
        <div class="card">
          <div class="card-header d-flex flex-column align-items-start pb-0">
            <div class="avatar bg-rgba-warning p-50 m-0">
              <div class="avatar-content">
                <i class="feather icon-package text-warning font-medium-5"></i>
              </div>
            </div>
            @php
              $reimbursements =  \App\Reimbursement::all();

    $reimbursementClaimReceived = $reimbursements->pluck('amount')->sum();
    $reimbursementClaimRejected = $reimbursements->where('rejected',1)->pluck('amount')->sum();
    $reimbursementClaimProcessed = $reimbursements->where('reimbursed',1)->pluck('amount')->sum();
    $reimbursementClaimPending = $reimbursementClaimReceived - $reimbursementClaimProcessed - $reimbursementClaimRejected;
            @endphp
            <h2 class="text-bold-700 mt-1 mb-25">{{ $reimbursementClaimPending }}</h2>
            <p class="mb-0">Pending Reimbursement</p>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-12">
        <div class="card">
          <div class="card-header d-flex flex-column align-items-start pb-0">
            <div class="avatar bg-rgba-warning p-50 m-0">
              <div class="avatar-content">
                <i class="feather icon-user text-danger font-medium-5"></i>
              </div>
            </div>
            <h2 class="text-bold-700 mt-1 mb-25">{{ \App\Employee::all()->count() }}</h2>
            <p class="mb-0">Employee</p>
          </div>
        </div>
      </div>
    </div>
    @endif
    @if(request()->type == 'clients')

      <div class="row">
        <div class="col-md-12 text-center">
          <div class="card">
            <div class="card-header"><h3>Clients</h3></div>
          </div>
        </div>
      </div>
    @endif
    @php
      $status = ['ACTIVE', 'CANCELLED', 'BREATHER', 'INCOMPLETE', 'ON HOLD'];
    @endphp
    <div class="row">
      @if(request()->type == 'clients')
          @foreach($status as $clientStatus)
            <div class="col-lg-3 col-md-6 col-12">
              <a href="{{ route('view.client.status',['status'=>$clientStatus]) }}">

              <div class="card">
                <div class="card-header d-flex flex-column align-items-start pb-0">
                  <div class="avatar bg-rgba-warning p-50 m-0">
                    <div class="avatar-content">
                      <i class="feather icon-user text-danger font-medium-5"></i>
                    </div>
                  </div>

                    <h2
                      class="text-bold-700 mt-1 mb-25">{{ \App\Client\Package\SoldPackages::where('status',$clientStatus)->count() }}</h2>
                    <p class="mb-0">{{ strtoupper($clientStatus) }}</p>
                </div>
              </div>
              </a>
            </div>

        @endforeach
        @endif
    </div>
      @if(request()->type == 'clients')

        <div class="row">
          <div class="col-md-12 text-center">
            <div class="card">
              <div class="card-header"><h3>Clients By Branch</h3></div>
              <div class="card-body">
                <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                  <a href="{{ route('view.client.branch',['branch'=>'chandigarh']) }}">

                    <div class="card">
                      <div class="card-header d-flex flex-column align-items-start pb-0">
                        <div class="avatar bg-rgba-warning p-50 m-0">
                          <div class="avatar-content">
                            <i class="feather icon-user text-danger font-medium-5"></i>
                          </div>
                        </div>

                        <h2
                          class="text-bold-700 mt-1 mb-25">{{ \App\Client\Package\SoldPackages::where('branch','like','%chandigarh%')->count() }}</h2>
                        <p class="mb-0">{{ strtoupper('chandigarh') }}</p>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                  <a href="{{ route('view.client.branch',['branch'=>'delhi']) }}">

                    <div class="card">
                      <div class="card-header d-flex flex-column align-items-start pb-0">
                        <div class="avatar bg-rgba-warning p-50 m-0">
                          <div class="avatar-content">
                            <i class="feather icon-user text-danger font-medium-5"></i>
                          </div>
                        </div>

                        <h2
                          class="text-bold-700 mt-1 mb-25">{{ \App\Client\Package\SoldPackages::where('branch','like','%delhi%')->count() }}</h2>
                        <p class="mb-0">{{ strtoupper('delhi') }}</p>
                      </div>
                    </div>
                  </a>
                </div>
                </div>
                </div>
            </div>
          </div>
        </div>
      @endif
  </section>
  <!-- Dashboard Analytics end -->
@endsection

@section('vendor-script')
  <!-- vendor files -->
  <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/tether.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/shepherd.min.js')) }}"></script>
@endsection
@section('page-script')
  <!-- Page js files -->
  <script src="{{ asset(mix('js/scripts/pages/dashboard-analytics.js')) }}"></script>
@endsection

@extends('layouts/contentLayoutMaster')

@section('title', 'User Log')

@section('vendor-style')
  {{-- Page Css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/pickadate/pickadate.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">

@endsection

@section('page-style')
  {{-- Page Css files --}}
  <link rel="stylesheet" href="{{ asset(mix('css/plugins/forms/validation/form-validation.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/pages/app-user.css')) }}">

@endsection

@section('content')
  <!-- users edit start -->
  <section class="users-edit">
    <div class="card">
      <div class="card-content">
        <div class="card-body">
          <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item">
              <a class="nav-link d-flex align-items-center active" id="login-tab" data-toggle="tab" href="#login"
                 aria-controls="login" role="tab" aria-selected="true">
                <i class="feather icon-log-in mr-25"></i><span class="d-none d-sm-block">Login Log</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link d-flex align-items-center" id="activity-tab" data-toggle="tab" href="#activity"
                 aria-controls="activity" role="tab" aria-selected="false">
                <i class="feather icon-activity mr-25"></i><span class="d-none d-sm-block">Activity Log</span>
              </a>
            </li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="login" aria-labelledby="login-tab" role="tabpanel">


              <section>
                <div class="row">
                  <div class="col-12">
                    <div class="card">
                      <div class="card-content">
                        <div class="card-body card-dashboard">
                          <div class="table-responsive">
                            <table class="table zero-configuration table-striped">
                              <thead>
                              <tr>
                                <th>#</th>
                                <th>IP</th>
                                <th>Date Time</th>
                                <th>Browser</th>
                                <th>Platform</th>
                                <th>Device</th>
                                <th>Location</th>
                              </tr>
                              </thead>
                              <tbody>
                              @foreach($logs as $log)
                                <tr @if($loop->index == 0) style="color:green" @endif>
                                  <td>{{ $loop->index + 1 }}</td>
                                  <td>{{ $log->ip }}</td>
                                  <td>{{ \Carbon\Carbon::parse($log->time)->format('D F d, Y h:i:s A') }}</td>
                                  <td>{{ $log->browser }}</td>
                                  <td>{{ $log->platform }}</td>
                                  <td>{{ strtoupper($log->device) }}</td>
                                  <td>{{ $log->location }}</td>
                                </tr>
                              @endforeach
                              </tbody>
                              <tfoot>
                              <tr>
                                <th>#</th>
                                <th>IP</th>
                                <th>Date Time</th>
                                <th>Browser</th>
                                <th>Platform</th>
                                <th>Device</th>
                                <th>Location</th>
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




            </div>
            <div class="tab-pane" id="activity" aria-labelledby="activity-tab" role="tabpanel">


              <section>
                <div class="row">
                  <div class="col-12">
                    <div class="card">
                      <div class="card-header">
                        <strong>Activity Log for {{ \Carbon\Carbon::now()->format('D F d, Y')}}</strong>
                      </div>
                      <div class="card-content">
                        <div class="card-body card-dashboard">
                          <div class="table-responsive">
                            <table class="table zero-configuration table-striped">
                              <thead>
                              <tr>
                                <th>#</th>
                                <th>Activity Name</th>
                                <th>Model</th>
                                <th>Type</th>
                                <th>Date Time</th>
                              </tr>
                              </thead>
                              <tbody>
                              </tbody>
                              <tfoot>
                              <tr>
                                <th>#</th>
                                <th>Activity Name</th>
                                <th>Model</th>
                                <th>Type</th>
                                <th>Date Time</th>
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




            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- users edit ends -->
@endsection

@section('vendor-script')

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
  <script src="{{ asset(mix('js/scripts/pages/app-user.js')) }}"></script>
  <script src="{{ asset(mix('js/scripts/navs/navs.js')) }}"></script>
  <script src="{{ asset(mix('js/scripts/datatables/datatable.js')) }}"></script>\
  <script>
  </script>

@endsection

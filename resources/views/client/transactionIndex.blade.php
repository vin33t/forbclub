@extends('layouts/contentLayoutMaster')

@section('title', 'Requests')

@section('vendor-style')
  {{-- vendor css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
@endsection

@section('content')

  <div class="row">

    <div class="col-lg-3 col-md-6 col-12">
      <div class="card">
        <a href="{{ route('download.axis.mis') }}">
          <div class="card-header d-flex flex-column align-items-start pb-0">
            <div class="avatar bg-rgba-warning p-50 m-0">
              <div class="avatar-content">
                <i class="feather icon-package text-warning font-medium-5"></i>
              </div>
            </div>
            <h2 class="text-bold-700 mt-1 mb-25">Download Axis Mis</h2>
            {{--        <p class="mb-0">Refund Requests</p>--}}
          </div>
        </a>

      </div>

    </div>
    <div class="col-lg-3 col-md-6 col-12">
      <div class="card">
        <a href="{{ route('upload.axis.mis') }}">
          <div class="card-header d-flex flex-column align-items-start pb-0">
            <div class="avatar bg-rgba-danger p-50 m-0">
              <div class="avatar-content">
                <i class="feather icon-package text-danger font-medium-5"></i>
              </div>
            </div>
            <h2 class="text-bold-700 mt-1 mb-25">Upload Axis MIS</h2>
            {{--        <p class="mb-0">Refund Requests</p>--}}
          </div>
        </a>

      </div>

    </div>

    <div class="col-lg-3 col-md-6 col-12">
      <div class="card">
        <a href="{{ route('download.cheques.view') }}">
          <div class="card-header d-flex flex-column align-items-start pb-0">
            <div class="avatar bg-rgba-danger p-50 m-0">
              <div class="avatar-content">
                <i class="feather icon-package text-danger font-medium-5"></i>
              </div>
            </div>
            <h2 class="text-bold-700 mt-1 mb-25">Cheques</h2>
            {{--        <p class="mb-0">Refund Requests</p>--}}
          </div>
        </a>

      </div>

    </div>
    <div class="col-lg-3 col-md-6 col-12">
      <div class="card">
        <a href="{{ route('upload.transaction') }}">
          <div class="card-header d-flex flex-column align-items-start pb-0">
            <div class="avatar bg-rgba-danger p-50 m-0">
              <div class="avatar-content">
                <i class="feather icon-package text-danger font-medium-5"></i>
              </div>
            </div>
            <h2 class="text-bold-700 mt-1 mb-25">Upload Transactions</h2>
            {{--        <p class="mb-0">Refund Requests</p>--}}
          </div>
        </a>

      </div>

    </div>

  </div>

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

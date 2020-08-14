@extends('layouts/contentLayoutMaster')

@section('title', 'Employee')

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
              <a class="nav-link d-flex align-items-center active" id="account-tab" data-toggle="tab" href="#roles"
                 aria-controls="roles" role="tab" aria-selected="true">
                <i class="feather icon-user mr-25"></i><span class="d-none d-sm-block">Employee</span>
              </a>
            </li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="roles" aria-labelledby="roles-tab" role="tabpanel">


              <section>
                <div class="row">
                  <div class="col-12">
                    <div class="card">
                      <div class="card-header">
                        <h4 class="card-title">{{ $employees }} Employees
                        </h4>
                      </div>
                      <div class="card-content">
                        <div class="card-body card-dashboard">
                          <p class="card-text">
                          <div class="table-responsive">
                            <table class="table zero-configuration">
                              <thead>
                              <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Department</th>
                                <th>Roles & Permissions</th>
                                <th>Action</th>
                              </tr>
                              </thead>
                              <tbody>
                              </tbody>
                              <tfoot>
                              <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Department</th>
                                <th>Roles & Permissions</th>
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


            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <form id="frm-suspend" action="{{ route('suspend.employee.login') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="employeeId" id="frm-suspend-id">
  </form>

  <form id="frm-activate" action="{{ route('activate.employee.login') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="employeeId" id="frm-activate-id">
  </form>

  <form id="frm-create" action="{{ route('create.employee.login') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="employeeId" id="frm-create-id">
  </form>
  <!-- users edit ends -->
@endsection

@section('vendor-script')
  {{-- Vendor js files --}}
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/validation/jqBootstrapValidation.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>
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
{{--  <script src="{{ asset(mix('js/scripts/datatables/datatable.js')) }}"></script>--}}
  <script src="{{ asset(mix('js/scripts/popover/popover.js')) }}"></script>
  <script>
    $('.zero-configuration').DataTable({
      processing: true,
      serverSide: true,
      ajax: "{{ route('employee') }}",
      columns: [
        {data: 'DT_RowIndex', name: 'DT_RowIndex'},
        {data: 'name', name: 'name'},
        {data: 'phone', name: 'phone'},
        {data: 'email', name: 'email'},
        {data: 'department', name: 'department'},
        {data: 'roleAndPermission', name: 'roleAndPermission'},
        {data: 'login', name: 'login', orderable: false, searchable: false},
      ]
    });
  </script>

@endsection

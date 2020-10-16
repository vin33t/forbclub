@extends('layouts/contentLayoutMaster')

@section('title', 'Create Role')

@section('vendor-style')
  {{-- Page Css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/pickadate/pickadate.css')) }}">

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
          <!-- users edit media object ends -->
          <!-- users edit account form start -->
          <form novalidate method="POST" action="{{ route('create.role') }}">
            @csrf
            <div class="row">
              <div class="col-12 col-sm-6">
                <div class="form-group">
                  <div class="controls">
                    <label>Role Name</label>
                    <input type="text" class="form-control" placeholder="Role Name" value="" name="roleName" required
                           data-validation-required-message="Please Enter a Role Name">
                  </div>
                </div>
              </div>
              <div class="col-12 col-sm-6">
              </div>
              <div class="col-12">
                <div class="table-responsive border rounded px-1 ">
                  <h6 class="border-bottom py-1 mx-1 mb-0 font-medium-2"><i
                      class="feather icon-lock mr-50 "></i>Permissions</h6>

                  <table class="table table-borderless">
                    <thead>
                    <tr>
                      <th>Module</th>
                      <th>View</th>
                      <th>Update</th>
                      <th>Create</th>
                      <th>Delete</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach(\App\PermissionGroup::all() as $group)
                      <tr>
                        <td>{{ $group->group_name }}</td>
                        @foreach(explode(',',$group->group_permissions) as $permission)
                          <td>
                            <div class="custom-control custom-checkbox"><input type="checkbox" id="{{$permission}}-checkbox"
                                                                               class="custom-control-input"
                                                                               name="{{$permission}}">
                              <label class="custom-control-label" for="{{$permission}}-checkbox"></label>
                            </div>
                          </td>
                        @endforeach
                      </tr>
                    @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Create Role</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
  <!-- users edit ends -->
@endsection

@section('vendor-script')
  {{-- Vendor js files --}}
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/validation/jqBootstrapValidation.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>

@endsection

@section('page-script')
  {{-- Page js files --}}
  <script src="{{ asset(mix('js/scripts/pages/app-user.js')) }}"></script>

@endsection

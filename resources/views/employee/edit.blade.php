@extends('layouts/contentLayoutMaster')

@section('title', 'Edit Employee')

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
          <form novalidate method="POST" action="{{ route('edit.employee',['id'=>$employee->id]) }}">
            @csrf
            <div class="row">
              <div class="col-12 col-sm-3">
                <div class="form-group">
                  <div class="controls">
                    <label>Employee Name</label>
                    <input type="text" class="form-control" placeholder="Employee Name" value="{{ $employee->name }}"
                           name="emplName" required
                           data-validation-required-message="Employee name can not be empty">
                  </div>
                </div>
              </div>
              <div class="col-12 col-sm-3">
                <div class="form-group">
                  <div class="controls">
                    <label>Employee Phone</label>
                    <input type="number" class="form-control" placeholder="Employee Phone"
                           value="{{ $employee->phone }}" name="emplPhone" required
                           data-validation-required-message="Employee phone can not be empty">
                  </div>
                </div>
              </div>
              <div class="col-12 col-sm-3">
                <div class="form-group">
                  <div class="controls">
                    <label>Employee Email</label>
                    <input type="email" class="form-control" placeholder="Employee Email" value="{{ $employee->email }}"
                           name="emplEmail" required
                           data-validation-required-message="Employee email can not be empty">
                  </div>
                </div>
              </div>
              <div class="col-12 col-sm-3">
                <div class="form-group">
                  <div class="controls">
                    <label>Employee Department</label>
                    <input type="text" class="form-control" placeholder="Employee Department"
                           value="{{ $employee->department }}" name="emplDepartment" required
                           data-validation-required-message="Employee Department can not be empty">
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              @if($employee->User)
                <div class="col-sm-6 col-6">
                  <div class="text-bold-600 font-medium-2">
                    Employee Roles
                  </div>
                  <p>Use <code>Roles</code> to assign access to different parts of the app. One employee can have more
                    than one <code>Role</code> attached. Super Admin role contains the highest level of privileges.</p>
                  <div class="form-group">
                    <select class="select2 form-control" multiple="multiple" name="roles[]">
                      @foreach(\Spatie\Permission\Models\Role::all() as $role)
                        <option value="{{ $role->name }}"
                                @if($employee->User && $employee->User->hasRole($role->name)) selected @endif >{{ str_replace('-',' ',strtoupper($role->name)) }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                @if($employee->User && $employee->User->roles->count())
                <div class="col-sm-6 col-6">
                  Remove all the Roles and Convert them to separate permissions
                  <br>
                  <a href="{{ route('convert.rolesToPermissions',['userId'=>$employee->User->id]) }}">Convert</a>
                </div>
                @endif
                <div class="col-12">
                  <code>Only Select if you want the user to have a specific permission that the Role assigned might not
                    have.</code>
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
                                                                                 name="{{$permission}}" @if($employee->User->can($permission)) checked @endif >
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
              @endif
              <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Update Employee</button>
                {{--                <button type="reset" class="btn btn-outline-warning">Reset</button>--}}
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
  <script src="{{ asset(mix('js/scripts/forms/select/form-select2.js')) }}"></script>


@endsection

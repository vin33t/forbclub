@extends('layouts/contentLayoutMaster')

@section('title', 'Edit User Page')

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
                <i class="feather icon-user mr-25"></i><span class="d-none d-sm-block">Roles</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link d-flex align-items-center" id="information-tab" data-toggle="tab" href="#information"
                 aria-controls="information" role="tab" aria-selected="false">
                <i class="feather icon-info mr-25"></i><span class="d-none d-sm-block">Permission</span>
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
                        <h4 class="card-title">Roles <a  href="{{ route('create.role') }}"><button type="button" class="btn btn-icon btn-warning mr-1 mb-1"><i class="feather icon-plus"></i></button></a>
                        </h4>
                      </div>
                      <div class="card-content">
                        <div class="card-body card-dashboard">
                          <p class="card-text">Roles are collection of Permissions assigned to Employee to control their access inside the app.</p>
                          <div class="table-responsive">
                            <table class="table zero-configuration">
                              <thead>
                              <tr>
                                <th>Role Name</th>
                                <th>Employees</th>
                                <th>Permissions</th>
                                <th>Actions</th>
                              </tr>
                              </thead>
                              <tbody>
                              @foreach(\Spatie\Permission\Models\Role::all() as $role)
                                <tr>
                                  <td>{{ $role->name }}</td>
                                  <td>{{ $role->users()->count() }}</td>
                                  <td>{{ $role->permissions()->count() }}</td>
                                  <td>
                                    <a  href="{{ route('create.role') }}"><button type="button" class="btn btn-icon btn-primary mr-1 mb-1"><i class="feather icon-edit"></i></button></a>
{{--                                    <button type="button" class="btn btn-icon btn-danger mr-1 mb-1"><i class="feather icon-trash" id="type-success"></i></button>--}}
                                  </td>
                                </tr>
                              @endforeach
                              </tbody>
                              <tfoot>
                              <tr>
                                <th>Role Name</th>
                                <th>Employees</th>
                                <th>Permissions</th>
                                <th>Actions</th>
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
            <div class="tab-pane" id="information" aria-labelledby="information-tab" role="tabpanel">
              <!-- users edit Info form start -->
              <form novalidate>
                <div class="row mt-1">
                  <div class="col-12 col-sm-6">
                    <h5 class="mb-1"><i class="feather icon-user mr-25"></i>Personal Information</h5>
                    <div class="row">
                      <div class="col-12">
                        <div class="form-group">
                          <div class="controls">
                            <label>Birth date</label>
                            <input type="text" class="form-control birthdate-picker" required placeholder="Birth date"
                                   data-validation-required-message="This birthdate field is required">
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="controls">
                        <label>Mobile</label>
                        <input type="text" class="form-control" value="&#43;6595895857"
                               placeholder="Mobile number here..."
                               data-validation-required-message="This mobile number is required">
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="controls">
                        <label>Website</label>
                        <input type="text" class="form-control" required placeholder="Website here..."
                               value="https://rowboat.com/insititious/Angelo"
                               data-validation-required-message="This Website field is required">
                      </div>
                    </div>
                    <div class="form-group">
                      <label>Languages</label>
                      <select class="form-control" id="users-language-select2" multiple="multiple">
                        <option value="English" selected>English</option>
                        <option value="Spanish">Spanish</option>
                        <option value="French">French</option>
                        <option value="Russian">Russian</option>
                        <option value="German">German</option>
                        <option value="Arabic" selected>Arabic</option>
                        <option value="Sanskrit">Sanskrit</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label>Gender</label>
                      <ul class="list-unstyled mb-0">
                        <li class="d-inline-block mr-2">
                          <fieldset>
                            <div class="vs-radio-con">
                              <input type="radio" name="vueradio" checked value="false">
                              <span class="vs-radio">
                              <span class="vs-radio--border"></span>
                              <span class="vs-radio--circle"></span>
                            </span>
                              Male
                            </div>
                          </fieldset>
                        </li>
                        <li class="d-inline-block mr-2">
                          <fieldset>
                            <div class="vs-radio-con">
                              <input type="radio" name="vueradio" value="false">
                              <span class="vs-radio">
                              <span class="vs-radio--border"></span>
                              <span class="vs-radio--circle"></span>
                            </span>
                              Female
                            </div>
                          </fieldset>
                        </li>
                        <li class="d-inline-block mr-2">
                          <fieldset>
                            <div class="vs-radio-con">
                              <input type="radio" name="vueradio" value="false">
                              <span class="vs-radio">
                              <span class="vs-radio--border"></span>
                              <span class="vs-radio--circle"></span>
                            </span>
                              Other
                            </div>
                          </fieldset>
                        </li>

                      </ul>
                    </div>
                    <div class="form-group">
                      <label>Contact Options</label>
                      <ul class="list-unstyled mb-0">
                        <li class="d-inline-block mr-2">
                          <fieldset>
                            <div class="custom-control custom-checkbox">
                              <input type="checkbox" class="custom-control-input" checked name="customCheck1"
                                     id="customCheck1">
                              <label class="custom-control-label" for="customCheck1">Email</label>
                            </div>
                          </fieldset>
                        </li>
                        <li class="d-inline-block mr-2">
                          <fieldset>
                            <div class="custom-control custom-checkbox">
                              <input type="checkbox" class="custom-control-input" checked name="customCheck2"
                                     id="customCheck2">
                              <label class="custom-control-label" for="customCheck2">Message</label>
                            </div>
                          </fieldset>
                        </li>
                        <li class="d-inline-block mr-2">
                          <fieldset>
                            <div class="custom-control custom-checkbox">
                              <input type="checkbox" class="custom-control-input" name="customCheck3" id="customCheck3">
                              <label class="custom-control-label" for="customCheck3">Phone</label>
                            </div>
                          </fieldset>
                        </li>
                      </ul>
                    </div>

                  </div>
                  <div class="col-12 col-sm-6">
                    <h5 class="mb-1 mt-2 mt-sm-0"><i class="feather icon-map-pin mr-25"></i>Address</h5>
                    <div class="form-group">
                      <div class="controls">
                        <label>Address Line 1</label>
                        <input type="text" class="form-control" value="A-65, Belvedere Streets" required
                               placeholder="Address Line 1" data-validation-required-message="This Address field is required">
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="controls">
                        <label>Address Line 2</label>
                        <input type="text" class="form-control" required placeholder="Address Line 2"
                               data-validation-required-message="This Address field is required">
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="controls">
                        <label>Postcode</label>
                        <input type="text" class="form-control" required placeholder="postcode" value="1868"
                               data-validation-required-message="This Postcode field is required">
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="controls">
                        <label>City</label>
                        <input type="text" class="form-control" required value="New York"
                               data-validation-required-message="This Time Zone field is required">
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="controls">
                        <label>State</label>
                        <input type="text" class="form-control" required value="New York"
                               data-validation-required-message="This Time Zone field is required">
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="controls">
                        <label>Country</label>
                        <input type="text" class="form-control" required value="United Kingdom"
                               data-validation-required-message="This Time Zone field is required">
                      </div>
                    </div>
                  </div>
                  <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                    <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Save
                      Changes</button>
                    <button type="reset" class="btn btn-outline-warning">Reset</button>
                  </div>
                </div>
              </form>
              <!-- users edit Info form ends -->
            </div>
          </div>
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


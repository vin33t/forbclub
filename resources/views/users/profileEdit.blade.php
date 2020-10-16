@extends('layouts/contentLayoutMaster')

@section('title', 'Edit User')

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
  @php
    $user = \Illuminate\Support\Facades\Auth::user();
  @endphp
  <!-- users edit start -->
  <section class="users-edit">
    <div class="card">
      <div class="card-content">
        <div class="card-body">
          <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item">
              <a class="nav-link d-flex align-items-center active" id="account-tab" data-toggle="tab" href="#account"
                 aria-controls="account" role="tab" aria-selected="true">
                <i class="feather icon-user mr-25"></i><span class="d-none d-sm-block">Account</span>
              </a>
            </li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="account" aria-labelledby="account-tab" role="tabpanel">
              <!-- users edit media object start -->
              <div class="media mb-2">
                <a class="mr-2 my-25" href="#">
                  <img src="{{ asset('images/portrait/small/avatar-s-12.jpg') }}" alt="users avatar"
                       class="users-avatar-shadow rounded" height="64" width="64">
                </a>
                <div class="media-body mt-50">
                  <h4 class="media-heading">{{ $user->name }}</h4>
                  <div class="col-12 d-flex mt-1 px-0">
                    <a href="#" class="btn btn-primary d-none d-sm-block mr-75">Change</a>
                    <a href="#" class="btn btn-primary d-block d-sm-none mr-75"><i
                        class="feather icon-edit-1"></i></a>
                    <a href="#" class="btn btn-outline-danger d-none d-sm-block">Remove</a>
                    <a href="#" class="btn btn-outline-danger d-block d-sm-none"><i class="feather icon-trash-2"></i></a>
                  </div>
                </div>
              </div>
              <!-- users edit media object ends -->
              <!-- users edit account form start -->
              <form novalidate>
                <div class="row">
                  <div class="col-12 col-sm-6">
                    <div class="form-group">
                      <div class="controls">
                        <label>Name</label>
                        <input type="text" class="form-control" placeholder="Name" value="{{ $user->name }}" required
                               data-validation-required-message="This name field is required">
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="controls">
                        <label>E-mail</label>
                        <input type="email" class="form-control" placeholder="Email" value="{{ $user->email }}"
                               required data-validation-required-message="This email field is required">
                      </div>
                    </div>
                  </div>

                  <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                    <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Save
                      Changes</button>
                  </div>
                </div>
              </form>
              <!-- users edit account form ends -->
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
@endsection

@section('page-script')
  {{-- Page js files --}}
  <script src="{{ asset(mix('js/scripts/pages/app-user.js')) }}"></script>
  <script src="{{ asset(mix('js/scripts/navs/navs.js')) }}"></script>
@endsection


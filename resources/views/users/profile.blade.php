@extends('layouts/contentLayoutMaster')

@section('title', \Illuminate\Support\Facades\Auth::user()->name)

@section('page-style')
  {{-- Page Css files --}}
  <link rel="stylesheet" href="{{ asset(mix('css/pages/app-user.css')) }}">
@endsection

@section('content')
  <!-- page users view start -->
  @php
    $user = \Illuminate\Support\Facades\Auth::user();
  @endphp
  <section class="page-users-view">

    <div class="row">
      <!-- account start -->
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="card-title">Account</div>
            <div class="row">
              <div class="col-2 users-view-image">
                <img src="{{ $user->employee->photo }}" class="w-100 rounded mb-2"
                     alt="avatar">
{{--                <img src="{{ asset('images/portrait/small/avatar-default.png') }}" class="w-100 rounded mb-2"--}}
{{--                     alt="avatar">--}}
                <!-- height="150" width="150" -->
              </div>
              <div class="col-sm-4 col-12">
                <table>
                  <tr>
                    <td class="font-weight-bold">Name</td>
                    <td>{{ $user->name }}</td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">Email</td>
                    <td>{{ $user->email }}</td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">Last Login</td>
                    <td>
                      @php
                        $lastLogin = $user->LoginLog->last()
                      @endphp
                      {{ $lastLogin->ip }} | {{ Carbon\Carbon::parse($lastLogin->time)->format('D F d, y h:i:s A') }} | {{ $lastLogin->browser }} | {{ $lastLogin->platform }} | {{ $lastLogin->location }}
                    </td>
                  </tr>
                </table>
              </div>
              <div class="col-md-6 col-12 ">
                <table class="ml-0 ml-sm-0 ml-lg-0">
                  <tr>
                    <td class="font-weight-bold">Login Status</td>
                    <td>{{ $user->login_revoked ? 'Login Revoked' : 'Active'  }}</td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">Role</td>
                    <td>@foreach($user->roles as $role) {{ strtoupper(str_replace('-',' ',$role->name)) }}, @endforeach</td>
                  </tr>
                </table>
              </div>
              <div class="col-12">
{{--                <a href="{{ route('edit.profile',['userId'=>$user->id]) }}" class="btn btn-primary mr-1"><i class="feather icon-edit-1"></i> Edit</a>--}}
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- account end -->
      <!-- information start -->
      <div class="col-md-6 col-12 ">
        <div class="card">
          <div class="card-body">
            <div class="card-title mb-2">Information</div>
            <table>
              <tr>
                <td class="font-weight-bold">Mobile</td>
                <td>{{ $user->employee->phone }}</td>
              </tr>

              <tr>
                <td class="font-weight-bold">Department</td>
                <td>{{ $user->employee->department }}</td>
              </tr>
            </table>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-12 ">
        <div class="card">
          <div class="card-header">
            Change Password
          </div>
          <div class="card-body">
            @if (count($errors) > 0)
              <div class = "alert alert-danger">
                <ul>
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
            <form action="{{ route('update.profile.password') }}" method="POST">
              @csrf
              <div class="row">
                <div class="col-12">
                  <div class="form-group">
                    <div class="controls">
                      <label>Old Password</label>
                      <input type="password" name="old_password" class="form-control" placeholder="Old Password" required>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="controls">
                      <label>New Password</label>
                      <input type="password" name="password" class="form-control" placeholder="New Password" required>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="controls">
                      <label>Confirm New Password</label>
                      <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm New Password" required>
                    </div>
                  </div>

                </div>

                <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                  <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Update Password</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

{{--      <div class="col-md-12">--}}
{{--        <div class="card">--}}
{{--          <div class="card-header">--}}
{{--            Themes--}}
{{--          </div>--}}
{{--            <div class="card-body">--}}
{{--              --}}
{{--            </div>--}}
{{--        </div>--}}
{{--      </div>--}}
    </div>
  </section>
  <!-- page users view end -->
@endsection

@section('page-script')
  {{-- Page js files --}}
  <script src="{{ asset(mix('js/scripts/pages/app-user.js')) }}"></script>
@endsection

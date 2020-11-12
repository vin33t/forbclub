@extends('layouts/contentLayoutMaster')

@section('title', ' Clients')

@section('vendor-style')
  {{-- vendor css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
@endsection

@section('content')

  <!-- Zero configuration table -->
  <section id="basic-datatable">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">{{ $status }} Clients({{ $packages->count() }})</h4>
          </div>
          <div class="card-content">
            <div class="card-body card-dashboard">
              <div class="table-responsive">
                <table class="table zero-configuration">
                  <thead>
                  <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Enrollment Date</th>
                    <th>Valid Till</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Package</th>
                    <th>Price</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($packages as $package)
                    <tr>
                      <td>{{ $loop->index + 1 }}</td>
                      <td><a href="{{ route('view.client',['slug'=>$package->client->slug]) }}" target="_blank">{{ $package->client->name }}</a></td>
                      <td>{{ $package->enrollmentDate }}</td>
                      <td>{{  \Carbon\Carbon::parse($package->enrollmentDate)->addYears($package->productTenure)->format('d M, Y') }}</td>
                      <td>{{ $package->client->phone }}</td>
                      <td>{{ $package->client->email }}</td>
                      <td>{{ $package->productName }}</td>
                      <td>{{ $package->productCost }}</td>
                    </tr>
                  @endforeach
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Enrollment Date</th>
                    <th>Valid Till</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Package</th>
                    <th>Price</th>
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

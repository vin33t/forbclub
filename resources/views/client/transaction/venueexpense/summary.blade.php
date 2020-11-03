@extends('layouts/contentLayoutMaster')

@section('title', 'Venue Expense Summary')
@section('vendor-style')
  {{-- vendor css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
@endsection

@section('content')
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
<div class="row">
  <div class="col-md-12">
    <button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#addVenue" >Add Venue</button>
    <div class="modal fade" id="addVenue" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header ">
            <h5 class="modal-title"><strong>Add New Venue</strong></h5>
            <span aria-hidden="true" data-dismiss="modal">×</span>
          </div>
          <form action="{{ route('venue.add') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
              <div class="row">

                <div class="col-md-12">
                  <label for="venueDate">Venue Date</label>
                  <input type="date" name="venueDate" id="venueDate" class="form-control" required>
                </div>

                <div class="col-md-12">
                  <label for="Venue Name">Venue Name</label>
                  <input type="text" name="Venue Name" id="Venue Name" class="form-control"  required>
                </div>
                <div class="col-md-12">
                  <label for="venueLocation">Venue Location</label>
                  <input type="text" name="venueLocation" id="venueLocation" class="form-control" required>
                </div>

              </div>

            </div>
            <div class="modal-footer ">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-info">Add</button>
            </div>
          </form>
        </div>
      </div>
    </div>

  </div>
</div>


  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table zero-configuration">
              <thead>
              <tr>
                <th>Month</th>
                <th>Total Venue</th>
                <th>Venue Cost</th>
                <th>Stay</th>
                <th>Food</th>
                <th>Travel</th>
                <th>Other</th>
              </tr>
              </thead>
              <tbody>
              @foreach($venues as $venue)
                <tr>
                  <td><a href="{{ route('venue.expense',['month'=>$venue['rawMonth'],'year'=>$venue['rawYear']]) }}">{{ $venue['month'] }} </a></td>
                  <td>{{ $venue['totalVenues'] }}</td>
                  <td>{{ $venue['venueCost'] }}</td>
                  <td>{{ $venue['stayCost'] }}</td>
                  <td>{{ $venue['foodCost'] }}</td>
                  <td>{{ $venue['travelCost'] }}</td>
                  <td>{{ $venue['otherCost'] }}</td>
                </tr>
              @endforeach
              </tbody>
              <tfoot>
              <tr>
                <th>Month</th>
                <th>Total Venue</th>
                <th>Venue Cost</th>
                <th>Stay</th>
                <th>Food</th>
                <th>Travel</th>
                <th>Other</th>
              </tr>
              </tfoot>
            </table>
          </div>

        </div>
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
  <!-- Page js files -->
  <script src="{{ asset(mix('js/scripts/datatables/datatable.js')) }}"></script>
  <script src="{{ asset(mix('js/scripts/modal/components-modal.js')) }}"></script>
@endsection

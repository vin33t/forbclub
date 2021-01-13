@extends('layouts/contentLayoutMaster')

@section('title', 'Queries')
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
      <h3>Booking Queries</h3>
    </div>
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table venue-expense">
              <thead>
              <tr>
                <th>Received On</th>
                <th>Client</th>
                <th>Travel Date</th>
                <th>Destination</th>
                <th>Adults</th>
                <th>Kids</th>
                <th>Room</th>
                <th>Remarks</th>
              </tr>
              </thead>
                <tbody>
              @foreach($bookingQueries as $bQuery)
                <tr>
                  <td>{{ \Carbon\Carbon::parse($bQuery->created_at)->format('d-m-Y') }}</td>
                  <td><a href="{{ route('view.client',['slug'=>$bQuery->client->slug]) }}" target="_blank">{{ $bQuery->client->name }} ({{ $bQuery->client->latestPackage->mafNo }})</a></td>
                  <td>{{ \Carbon\Carbon::parse($bQuery->travelDate)->format('d-m-Y') }}</td>
                  <td>{{ $bQuery->destination }}</td>
                  <td>{{ $bQuery->adults }}</td>
                  <td>{{ $bQuery->kids }}</td>
                  <td>{{ $bQuery->rooms }}</td>
                  <td>{{ $bQuery->remarks }}</td>
                </tr>
              @endforeach
                </tbody>
              <tfoot>
              <tr>
                <th>Received On</th>
                <th>Client</th>
                <th>Travel Date</th>
                <th>Destination</th>
                <th>Adults</th>
                <th>Kids</th>
                <th>Room</th>
                <th>Remarks</th>
              </tr>
              </tfoot>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <h3>Other Queries</h3>
    </div>
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table venue-expense">
              <thead>
              <tr>
                <th>Received On</th>
                <th>Client</th>
                <th>Remarks</th>
              </tr>
              </thead>
                <tbody>
              @foreach($otherQueries as $oQuery)
                <tr>
                  <td>{{ \Carbon\Carbon::parse($oQuery->created_at)->format('d-m-Y') }}</td>
                  <td><a href="{{ route('view.client',['slug'=>$bQuery->client->slug]) }}" target="_blank">{{ $oQuery->client->name }} ({{ $oQuery->client->latestPackage->mafNo }})</a></td>
                  <td>{{ $oQuery->remarks }}</td>
                </tr>
              @endforeach
                </tbody>
              <tfoot>
              <tr>
                <th>Received On</th>
                <th>Client</th>
                <th>Remarks</th>
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
  {{--  <script src="{{ asset(mix('js/scripts/datatables/datatable.js')) }}"></script>--}}
  <script src="{{ asset(mix('js/scripts/modal/components-modal.js')) }}"></script>
  <script>
    $('.venue-expense').DataTable({
      "order": []
    });

  </script>
@endsection

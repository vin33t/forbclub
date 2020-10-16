
@extends('layouts/contentLayoutMaster')
@if($bank == 'yes')
@section('title', 'Yes Import History')
@else
@section('title', 'Axis Import History')
@endif
@section('vendor-style')
  {{-- vendor css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
@endsection

@section('content')

  <!-- Zero configuration table -->
  @if($bank == 'axis')
    <section id="basic-datatable">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Axis NACH Import History</h4>
          </div>
          <div class="card-content">
            <div class="card-body card-dashboard">
              <div class="table-responsive">
                <table class="table zero-configuration">
                  <thead>
                  <tr>
                    <th>Client Name</th>
                    <th>Phone</th>
                    <th>IFSC</th>
                    <th>Amount</th>
                    <th>UMRN</th>
                    <th>Debit Account</th>
                    <th>Transaction Date</th>
                    <th>Status</th>
                    <th>Reason</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($transactions as $transaction)
                    <tr>
                      <td>@if($transaction->client)<a href="{{ route('view.client',['slug'=>$transaction->client->slug]) }}">{{ $transaction->client->name }}</a>@endif</td>
                      <td>@if($transaction->client){{ $transaction->client->phone }}@endif</td>
                      <td>{{ $transaction->customer_ifsc }}</td>
                      <td>{{ $transaction->amount }}</td>
                      <td>{{ $transaction->umrn }}</td>
                      <td>{{ $transaction->customer_debit_ac }}</td>
                      <td>{{ $transaction->date_of_transaction }}</td>
                      <td>{{ $transaction->status_description }}</td>
                      <td>{{ $transaction->reason_description }}</td>
                    </tr>
                  @endforeach
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>Client Name</th>
                    <th>Phone</th>
                    <th>IFSC</th>
                    <th>Amount</th>
                    <th>UMRN</th>
                    <th>Debit Account</th>
                    <th>Transaction Date</th>
                    <th>Status</th>
                    <th>Reason</th>
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
  @endif
  @if($bank == 'yes')
    <section id="basic-datatable">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Yes NACH Import History</h4>
          </div>
          <div class="card-content">
            <div class="card-body card-dashboard">
              <div class="table-responsive">
                <table class="table zero-configuration">
                  <thead>
                  <tr>
                    <th>Client Name</th>
                    <th>Phone</th>
                    <th>Amount</th>
                    <th>Debit Account</th>
                    <th>Transaction Date</th>
                    <th>Status</th>
                    <th>Reason</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($transactions as $transaction)
                    <tr>
                      <td>@if($transaction->client)<a href="{{ route('view.client',['slug'=>$transaction->client->slug]) }}">{{ $transaction->client->name }}</a>@endif</td>
                      <td>@if($transaction->client){{ $transaction->client->phone }}@endif</td>
                      <td>{{ $transaction->AMOUNT }}</td>
                      <td>{{ $transaction->RECEIVER_ACCOUNT }}</td>
                      <td>{{ $transaction->VALUE_DATE }}</td>
                      <td>{{ $transaction->STATUS }}</td>
                      <td>{{ $transaction->REASON_CODE }}</td>
                    </tr>
                  @endforeach
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>Client Name</th>
                    <th>Phone</th>
                    <th>Amount</th>
                    <th>Debit Account</th>
                    <th>Transaction Date</th>
                    <th>Status</th>
                    <th>Reason</th>
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
  @endif
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

@extends('layouts/contentLayoutMaster')

@section('title', 'Upcoming Transactions')

@section('vendor-style')
  {{-- vendor css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
@endsection

@section('content')
  <ul class="nav nav-tabs tabs-design" id="myTab" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#holiday" role="tab" aria-controls="profile">Upcoming Holiday Transactions</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="profile-tab" data-toggle="tab" href="#refund" role="tab" aria-controls="profile">Upcoming Refund Transactions</a>
    </li>
  </ul>

  <div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="holiday" role="tabpanel" aria-labelledby="home-tab">
      <div class="row">
        <div class="col-md-12">
          <div class="card card-box">
            <div class="card-body ">
              <div class="table-responsive">
                <table class="table custom-table table-hover datatable zero-configuration">
                  <thead>
                  <tr>
                    <th>Sr. No.</th>
                    <th>Added On</th>
                    <th>Added By</th>
                    <th>Client</th>
                    <th>Service Type</th>
                    <th>Date Of Payment</th>
                    <th>Travel Date</th>
                    <th>Amount({{  $new_transactions->pluck('amount')->sum()  }})</th>
                  </tr>
                  </thead>
                  <tbody>
                  @php
                    $i = 1;
                    $foo = $new_transactions->where('amount','!=',0);
                  @endphp
                  @foreach($foo as $t)
                    <tr>
                      <th>{{$i++}}.</th>
                      <td>
                        {{Carbon\Carbon::parse($t->created_at)->format('Y-m-d')}} <br>
                        ({{Carbon\Carbon::parse($t->created_at)->format('l')}})
                      </td>

                      <td>
                        @if($t->FclpHolidays)
                          {{App\Employee::find($t->FclpHolidays->employee_id)->name}}
                        @elseif($t->ClientHolidayDetails)
                          @if($t->ClientHolidayDetails->ClientHoliday)
                            {{App\User::find($t->ClientHolidayDetails->ClientHoliday->converted_by)->name}}
                          @endif
                        @endif
                      </td>



                      <td>
                        @if($t->FclpHolidays)
                          @if($t->FclpHolidays->Holiday)
                            @if($t->FclpHolidays->Holiday->client)
                              <a href="{{ route('view.client',['slug'=>$t->FclpHolidays->Holiday->client->slug]) }}">{{$t->FclpHolidays->Holiday->client->name}}</a>
                            @endif
                          @endif
                        @elseif($t->ClientHolidayDetails)
                          @if($t->ClientHolidayDetails->ClientHoliday)
                            @if($t->ClientHolidayDetails->ClientHoliday->client)
                              <a href="{{ route('view.client',['slug'=>$t->ClientHolidayDetails->ClientHoliday->client->slug]) }}">{{$t->ClientHolidayDetails->ClientHoliday->client->name}}</a>
                            @endif
                          @endif
                        @endif
                      </td>


                      <td>
                        @if($t->FclpHolidays)
                          {{$t->FclpHolidays->service_type}}
                        @elseif($t->ClientHolidayDetails)
                          {{$t->ClientHolidayDetails->service_type}}
                        @endif
                      </td>




                      <td>
                        {{Carbon\Carbon::parse($t->date_of_payment)->format('Y-m-d')}} <br>
                        ({{Carbon\Carbon::parse($t->date_of_payment)->format('l')}})
                      </td>

                      <td>
                        @if($t->FclpHolidays)
                          @if($t->FclpHolidays->Holiday)
                            {{Carbon\Carbon::parse($t->FclpHolidays->Holiday->date_of_travel)->format('Y-m-d')}} <br>
                            ({{Carbon\Carbon::parse($t->FclpHolidays->Holiday->date_of_travel)->format('l')}})
                          @endif
                        @elseif($t->ClientHolidayDetails)
                          @if($t->ClientHolidayDetails->ClientHoliday)
                            {{Carbon\Carbon::parse($t->ClientHolidayDetails->ClientHoliday->date_of_travel)->format('Y-m-d')}} <br>
                            ({{Carbon\Carbon::parse($t->ClientHolidayDetails->ClientHoliday->date_of_travel)->format('l')}})
                          @endif
                        @endif
                      </td>

                      <td>{{$t->amount}}</td>
                    </tr>
                  @endforeach
                  {{-- @foreach($new_transactions->where('amount','!=',0) as $nt)
                      <tr>
                          <th>{{$i++}}.</th>
                          <td>{{Carbon\Carbon::parse($nt->created_at)->format('M d, Y')}}</td>

                          <td>{{App\User::find($nt->ClientHolidayDetails->ClientHoliday->converted_by)->name}}</td>
                          <td><a href="{{ route('client.show',['id'=>$nt->ClientHolidayDetails->ClientHoliday->client->id]) }}">{{$nt->ClientHolidayDetails->ClientHoliday->client->name}}</a></td>
                          <td>{{$nt->ClientHolidayDetails->service_type}}</td>
                          <td>{{Carbon\Carbon::parse($nt->date_of_payment)->format('M d, Y')}}</td>
                          <td>{{Carbon\Carbon::parse($nt->ClientHolidayDetails->ClientHoliday->date_of_travel)->format('l, M d, Y')}}</td>
                          <td>{{$nt->amount}}</td>
                      </tr>
                  @endforeach --}}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="tab-pane fade show" id="refund" role="tabpanel" aria-labelledby="home-tab">
      <div class="row">
        <div class="col-md-12">
          <div class="card card-box">
            <div class="card-body ">
              <div class="table-responsive">
                <table class="table custom-table table-hover datatable">
                  <thead>
                  <tr>
                    <th>Sr. No.</th>
                    <th>Client</th>
                    <th>Date Of Payment</th>
                    <th>Approved By</th>
                    <th>Approved On</th>
                    <th>Approved Remarks</th>
                    <th>Amount({{ number_format($refund_transactions->pluck('approval_accounts_amount')->sum()) }})</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($refund_transactions as $rt)
                    <tr>
                      <td> {{$loop->index + 1 }} </td>
                      <td> {{ $rt->client->name }} </td>
                      <td> {{ $rt->date_of_payment }} </td>
                      <td> {{ App\User::find($rt->approval_accounts_by)->name }} </td>
                      <td> {{ $rt->approval_accounts_datetime }} </td>
                      <td> {{ $rt->approval_accounts_remarks }} </td>
                      <td> {{ $rt->approval_accounts_amount }} </td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
              </div>
            </div>
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
  {{-- Page js files --}}
  <script src="{{ asset(mix('js/scripts/datatables/datatable.js')) }}"></script>

@endsection

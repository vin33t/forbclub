@extends('layouts/contentLayoutMaster')

@section('title', 'Download Cheques')
@section('vendor-style')
  {{-- vendor css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
@endsection
@php

  @endphp
@section('content')
  <div class="row">

    <div class="col-lg-6 col-12">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <form action="{{ route('download.cheques') }}" method="post">
              @csrf
              <div class="modal-body">
                <div class="row" style="display: none;">
                  <div class="col-md-6">
                    <label for="from">From:</label>
                    <input type="date" name="from" id="cheque_from" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label for="from">To:</label>
                    <input type="date" name="to" id="cheque_to" class="form-control">
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div id="reportrange2"
                         style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                      <i class="fa fa-calendar"></i>&nbsp;
                      <span></span> <i class="fa fa-caret-down"></i>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-info">Export</button>
                </div>
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
        <div class="card-header">
          <h3>Update Cheque Status
            <form action="{{ route('download.cheques.view') }}" method="get">
              @csrf
              <div class="row" style="display: none;">
                <div class="col-md-6">
                  <label for="from">From:</label>
                  <input type="date" name="cheque_from" id="cheque_from_update" class="form-control"
                         onchange="getCheques()">
                </div>
                <div class="col-md-6">
                  <label for="from">To:</label>
                  <input type="date" name="cheque_to" id="cheque_to_update" class="form-control"
                         onchange="getCheques()">
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div id="reportrange3"
                       style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span> <i class="fa fa-caret-down"></i>
                  </div>
                </div>
              </div>
              <button class="btn btn-primary btn-sm">View</button>
            </form>

          </h3>
          <h3>
            @if(request()->cheque_from and request()->cheque_to)
              Displaying cheques from {{ request()->cheque_from }} to {{ request()->cheque_to }}
            @else
            Displaying all Cheques
            @endif
          </h3>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table" id="pdc">
              <thead>
              <tr>
                <th>#</th>
                <th>Cheque Number</th>
                <th>Client</th>
                <th>Amount</th>
                <th>MICR Number</th>
                <th>Branch Name</th>
                <th>Date Of Execution</th>
                <th>Status</th>
                <th>Remarks</th>
                <th>Action</th>
              </tr>
              </thead>
              <tbody>

              @php
                if(request()->cheque_from and request()->cheque_to){
                    $pdcs = \App\PDC::where('status','unused')->whereBetween('date_of_execution',[request()->cheque_from,request()->cheque_to])->get();
                } else {
                  $pdcs = \App\PDC::where('status','unused')->get();
                }

              @endphp
              @foreach($pdcs as $pdc)
                <tr>
                  <td>{{ $loop->index + 1 }}</td>
                  <td>{{ $pdc->cheque_no }}</td>
                  <td><a href="{{ $pdc->Client ? route('view.client',['slug'=>$pdc->Client->slug]) : ''}}"
                         target="_blank">'{{ $pdc->Client ? $pdc->Client->name : '' }}</a></td>
                  <td>{{ $pdc->amount }}</td>
                  <td>{{ $pdc->micr_number }}</td>
                  <td>{{ $pdc->branch_name }}</td>
                  <td>{{ $pdc->date_of_execution }}</td>
                  <td>{{ $pdc->status }}</td>
                  <td>{{ $pdc->reason }}</td>
                  <td>
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editPdc{{$pdc->id}}">Update
                      Status
                    </button>
                    <div class="modal fade" id="editPdc{{$pdc->id}}" tabindex="-1" role="dialog">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header ">
                            <h5 class="modal-title"><strong>Add PDC</strong></h5>
                            <span aria-hidden="true" data-dismiss="modal">×</span>
                          </div>
                          <form action="{{ route('update.pdc') }}" method="post">
                            @csrf
                            <div class="modal-body">
                              <div class="row">
                                <input type="hidden" value="{{ $pdc->id }}" name="pdc">
                                <div class="col-md-12">
                                  <label for="cheque_number">Cheque Number</label>
                                  <input type="text" name="cheque_number" id="cheque_number" class="form-control"
                                         required="" value="{{ $pdc->cheque_no }}">
                                </div>
                                <div class="col-md-12">
                                  <label for="date_of_execution">Cheque Date</label>
                                  <input type="date" name="date_of_execution" id="date_of_execution"
                                         class="form-control" required="" value="{{ $pdc->date_of_execution }}">
                                </div>
                                <div class="col-md-12">
                                  <label for="amount">Amount</label>
                                  <input type="number" name="amount" id="amount" class="form-control" required=""
                                         value="{{ $pdc->amount }}">
                                </div>
                                <div class="col-md-12">
                                  <label for="micr_number">MICR Number</label>
                                  <input type="text" name="micr_number" id="micr_number" class="form-control"
                                         required="" value="{{ $pdc->micr_number }}">
                                </div>
                                <div class="col-md-12">
                                  <label for="remarks0" class="pull-left" origfor="remarks">Remarks</label>
                                  <textarea name="remarks" class="form-control">{{ $pdc->remarks }}</textarea>
                                </div>
                                <div class="col-md-12">
                                  <label for="remarks0" class="pull-left" origfor="remarks">Status</label>
                                  <select name="status" class="form-control">
                                    <option value="">--SELECT--</option>
                                    <option value="unused">Unused</option>
                                    <option value="CLEARED">Cleared</option>
                                    <option value="BOUNCED">Bounced</option>
                                  </select>
                                </div>
                              </div>

                            </div>
                            <div class="modal-footer ">
                              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                              <button type="submit" class="btn btn-info">Update</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              @endforeach
              </tbody>
              <tfoot>
              <tr>
                <th>#</th>
                <th>Cheque Number</th>
                <th>Client</th>
                <th>Amount</th>
                <th>MICR Number</th>
                <th>Branch Name</th>
                <th>Date Of Execution</th>
                <th>Status</th>
                <th>Remarks</th>
                <th>Action</th>
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
  <script src="https://cdn.datatables.net/plug-ins/1.10.15/api/row().show().js"></script>
@endsection



@section('page-script')
  <!-- Page js files -->
  <script src="{{ asset(mix('js/scripts/datatables/datatable.js')) }}"></script>
  <script src="{{ asset(mix('js/scripts/modal/components-modal.js')) }}"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
  <script src="{{ asset(mix('js/scripts/modal/components-modal.js')) }}"></script>

  <script>

    $(function () {

      var start = moment().subtract(29, 'days');
      var end = moment();

      var start2 = moment().subtract(29, 'days');
      var end2 = moment();

      function cb(start, end) {
        $('#reportrange2 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        document.getElementById("cheque_from").value = start.format('YYYY-MM-DD');
        document.getElementById("cheque_to").value = end.format('YYYY-MM-DD');
      }

      function cb2(start, end) {
        $('#reportrange3 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        document.getElementById("cheque_from_update").value = start.format('YYYY-MM-DD');
        document.getElementById("cheque_to_update").value = end.format('YYYY-MM-DD');
      }

      $('#reportrange2').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
      }, cb);
      $('#reportrange3').daterangepicker({
        startDate: start2,
        endDate: end2,
        ranges: {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
      }, cb2);

      cb(start, end);
      // cb2(start2, end2);

    });
  </script>
  <script>
    const dataTable = $('#pdc').DataTable();
  </script>

@endsection

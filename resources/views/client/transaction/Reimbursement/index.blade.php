@extends('layouts/contentLayoutMaster')

@section('title', 'Reimbursement')
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
  <div class="col-lg-6 col-12">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
{{--            <div class="row">--}}
{{--              <div class="col-md-4">--}}
{{--                <label for="month">Month</label>--}}
{{--                <select name="month" id="month" class="form-control" required>--}}
{{--                  <option value="">--Select Month--</option>--}}
{{--                  <option value="January">January</option>--}}
{{--                  <option value="February">February</option>--}}
{{--                  <option value="March">March</option>--}}
{{--                  <option value="April">April</option>--}}
{{--                  <option value="May">May</option>--}}
{{--                  <option value="June">June</option>--}}
{{--                  <option value="July">July</option>--}}
{{--                  <option value="August">August</option>--}}
{{--                  <option value="September">September</option>--}}
{{--                  <option value="October">October</option>--}}
{{--                  <option value="November">November</option>--}}
{{--                  <option value="December">December</option>--}}
{{--                </select>--}}
{{--              </div>--}}
{{--              <div class="col-md-4">--}}
{{--                <label for="year">Year</label>--}}
{{--                <select name="year" id="year" class="form-control" required>--}}
{{--                  <option value="">--Select Month--</option>--}}
{{--                  <option value="2020">2020</option>--}}
{{--                  <option value="2021">2021</option>--}}
{{--                </select>--}}
{{--              </div><div class="col-md-4">--}}
{{--                <button type="submit" class="btn btn-primary btn-sm">View</button>--}}
{{--              </div>--}}
{{--            </div>--}}

          </div>
            <div class="card-body">
            <strong>Total Reimbursements: </strong>{{  \App\Reimbursement::all()->pluck('amount')->sum()  }}<br>
            <strong>Paid Reimbursements</strong>{{ \App\Reimbursement::where('reimbursed',1)->get()->pluck('amount')->sum() }} <br>
            <strong>Unpaid Paid Reimbursements</strong>{{ \App\Reimbursement::where('reimbursed',0)->get()->pluck('amount')->sum() }}<br>
              <button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#addNewReimbursement" >Add new</button>
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
              <th>Employee</th>
              <th>Expense Date</th>
              <th>Amount</th>
              <th>Expense Type</th>
              <th>Remarks</th>
              <th>Bill</th>
              <th>Reimbursement Status</th>
            </tr>
            </thead>
            <tbody>
            @foreach(\App\Reimbursement::all() as $reimbursement)
            <tr>
              <td>{{ $reimbursement->employee->name }}</td>
              <td>{{ $reimbursement->expenseDate }}</td>
              <td>{{ $reimbursement->amount }}</td>
              <td>{{ $reimbursement->expenseType }}</td>
              <td>{{ $reimbursement->remarks }}</td>
              <td><a href="{{ asset('/uploads/'.$reimbursement->expenseBill) }}">Download</a></td>
              <td>
                @if(!$reimbursement->reimbursed)
                  <a href="javascript:void(0)"  data-toggle="modal" data-target="#reimburse{{$reimbursement->id}}">Not Reimbursed</a>
                  <div class="modal fade" id="reimburse{{$reimbursement->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLongTitle">Reimburse</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <form action="{{ route('reimbursement.pay') }}" method="POST">
                          @csrf
                        <div class="modal-body">
                          <div class="row">
                            <div class="col-md-12">
                              <input type="hidden" name="id" value="{{ $reimbursement->id }}">
                              <label for="reimbursementDate">Reimbursement Date</label>
                              <input type="date" name="reimbursementDate" required class="form-control">
                            </div>
                            <div class="col-md-12">
                              <label for="reimbursementRemarks">Reimbursement Remarks</label>
                              <textarea name="reimbursementRemarks" id="" cols="30" rows="10" class="form-control" required></textarea>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                          <button type="submit" class="btn btn-primary">Reimburse</button>
                        </div>
                        </form>
                      </div>
                    </div>
                  </div>
                @else
                  Reimbursed On: {{ $reimbursement->reimbursedOn }} <br>
                  Remarks: {{ $reimbursement->reimbursedRemarks }}
                @endif
              </td>
            </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
              <th>Employee</th>
              <th>Expense Date</th>
              <th>Amount</th>
              <th>Expense Type</th>
              <th>Remarks</th>
              <th>Bill</th>
              <th>Reimbursement Status</th>
            </tr>
            </tfoot>
          </table>
        </div>

      </div>
    </div>
  </div>
  </div>


  <div class="modal fade" id="addNewReimbursement" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header ">
          <h5 class="modal-title"><strong>Add Reimbursement</strong></h5>
          <span aria-hidden="true" data-dismiss="modal">×</span>
        </div>
        <form action="{{ route('reimbursement.add') }}" method="post" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <label for="employee">Employee</label>
                <select name="employee" id="employee" class="form-control">
                  <option value="">--Select Employee--</option>
                  @foreach(\App\Employee::all() as $employee)
                  <option value="{{ $employee->id  }}">{{ $employee->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-12">
                <label for="expenseDate">Expense Date</label>
                <input type="date" name="expenseDate" id="expenseDate" class="form-control" required>
              </div>

              <div class="col-md-12">
                <label for="expenseType">Expense Date</label>
                <input type="text" name="expenseType" id="expenseType" class="form-control" placeholder="Food/Travel/Stay etc." required>
              </div>
              <div class="col-md-12">
                <label for="amount">Amount</label>
                <input type="number" name="amount" id="amount" class="form-control" required>
              </div>
              <div class="col-md-12">
                <label for="expenseBill">Expense Bill (PDF Only)</label>
                <input type="file" name="expenseBill" id="expenseBill" class="form-control"  accept="application/pdf" required>
              </div>
              <div class="col-md-12">
                <label for="remarks" class="pull-left">Remarks</label>
                <textarea name="remarks" id="remarks" class="form-control"></textarea>
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

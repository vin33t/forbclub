@extends('layouts/contentLayoutMaster')

@section('title', 'Reimbursement Summary')
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

          </div>
          <div class="card-body">
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
            <table class="table reimbursementSummary">
              <thead>
              <tr>
                <th>Month</th>
                <th>Received</th>
                <th>Rejected</th>
                <th>Processed</th>
                <th>Pending</th>
              </tr>
              </thead>
              <tbody>
              @foreach($reimbursements->reverse() as $reimbursement)
                <tr>
                  <td><a href="{{ route('reimbursement.index',['month'=>$reimbursement['rawMonth'],'year'=>$reimbursement['rawYear']]) }}">{{ $reimbursement['month'] }} </a></td>
                  <td>{{ $reimbursement['received'] }}</td>
                  <td>{{ $reimbursement['rejected'] }}</td>
                  <td>{{ $reimbursement['processed'] }}</td>
                  <td>{{ $reimbursement['pending'] }}</td>
                </tr>
              @endforeach
              </tbody>
              <tfoot>
              <tr>
                <th>Month</th>
                <th>Received</th>
                <th>Rejected</th>
                <th>Processed</th>
                <th>Pending</th>
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
{{--  <script src="{{ asset(mix('js/scripts/datatables/datatable.js')) }}"></script>--}}
  <script src="{{ asset(mix('js/scripts/modal/components-modal.js')) }}"></script>
  <script>
    <script>
    $('.reimbursementSummary').DataTable({
      "order": []
    });

  </script>
@endsection

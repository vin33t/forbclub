@extends('layouts/contentLayoutMaster')

@section('title', 'Venue Expense')
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
            <strong>Total Venue Expense: </strong>{{ \App\VenueExpenses::all()->pluck('expense_amount')->sum() }}<br>
            <strong>Paid Venue Expense</strong>{{ \App\VenueExpenses::where('paid',1)->get()->pluck('expense_amount')->sum() }} <br>
            <button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#addVenueExpense" >Add Venue</button>
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
                <th>Venue Name</th>
                <th>Venue Date</th>
                <th>Venue Location</th>
                <th>Total Expense</th>
              </tr>
              </thead>
              <tbody>
              @foreach(\App\Venue::all() as $venue)
                <tr>
                  <td>{{ $venue->venue_name }}</td>
                  <td>{{ $venue->venue_date }}</td>
                  <td>{{ $venue->venue_location }}</td>
                  <td>
                    {{ $venue->Expense->pluck('expense_amount')->sum() }}
                   <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addVenueExpense{{ $venue->id }}">Add Expense</button>
                    <div class="modal fade" id="addVenueExpense{{ $venue->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Add Expense For Venue: {{ $venue->venue_name }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <form action="{{ route('venue.add.expense') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ $venue->id }}">
                          <div class="modal-body">
                              <div class="row">
                                <div class="col-md-12">
                                  <label for="expenseName">Expense Name</label>
                                  <input type="text" name="expenseName" class="form-control" required>
                                </div>
                                <div class="col-md-12">
                                  <label for="expenseAmount">Expense Amount</label>
                                  <input type="number" name="expenseAmount" class="form-control" required>
                                </div>
                                <div class="col-md-12">
                                  <label for="expenseDetails">Expense Details</label>
                                  <textarea name="expenseDetails" id="" cols="30" rows="10" class="form-control" required></textarea>
                                </div>
                                <div class="col-md-12">
                                  <label for="expenseBill">Expense Bill (PDF Only)</label>
                                  <input type="file" name="expenseBill" id="expenseBill" class="form-control"  accept="application/pdf" required>
                                </div>
                              </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Add Expense</button>
                          </div>
                          </form>
                        </div>
                      </div>
                    </div>

                    @if($venue->Expense->count())
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#viewVenueExpense{{ $venue->id }}">View Expenses</button>

                    <div class="modal fade" id="viewVenueExpense{{ $venue->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Venue: {{ $venue->venue_name }} Expenses</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="table-responsive">
                            <table class="table zero-configuration">
                              <thead>
                              <tr>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Details</th>
                                <th>Bill</th>
                              </tr>
                              </thead>
                              <tbody>
                                @foreach($venue->Expense as $expense)
                                  <tr>
                              <td>{{ $expense->expense_name }}</td>
                              <td>{{ $expense->expense_amount }}</td>
                              <td>{{ $expense->expense_details }}</td>
{{--                              <td><a href="{{ asset('/storage/uploads/'.$expense->expenseBill) }}">Download</a></td>--}}
                              <td><a href="{{ asset('/storage/uploads/'.$expense->expenseBill) }}">Download</a></td>
                                  </tr>
                              </tbody>

                              @endforeach
                              <tfoot>
                              <tr>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Details</th>
                                <th>Bill</th>
                              </tr>
                              </tfoot>
                            </table>
                          </div>

                        </div>
                      </div>
                    </div>
                      @endif
                  </td>
                </tr>
              @endforeach
              </tbody>
              <tfoot>
              <tr>
                <th>Venue Name</th>
                <th>Venue Date</th>
                <th>Venue Location</th>
                <th>Total Expense</th>
              </tr>
              </tfoot>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>


  <div class="modal fade" id="addVenueExpense" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header ">
          <h5 class="modal-title"><strong>Add Venue Expense</strong></h5>
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

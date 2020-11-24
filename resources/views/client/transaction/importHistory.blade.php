
@extends('layouts/contentLayoutMaster')

@section('title', 'Import History')

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
            <h4 class="card-title">Axis NACH Import History</h4>
          </div>
          <div class="card-content">
            <div class="card-body card-dashboard">
              <div class="table-responsive">
                <table class="table zero-configuration">
                  <thead>
                  <tr>
                    <th>Month</th>
                    <th>Year</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Success Amount</th>
                    <th>Failure Amount</th>
                    <th>Transactions</th>
                    <th>Failed</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach(\App\Client\Transaction\AxisNachPaymentMeta::all() as $meta)
                    <tr>
                      <td> <a href="{{ route('display.transaction.nach.import.history.details',['importId'=>$meta->id,'bank'=>'axis']) }}">{{ \Carbon\Carbon::parse($meta->upload_date)->format('F') }}                    </a>
                      </td>
                      <td>{{ \Carbon\Carbon::parse($meta->upload_date)->format('Y') }}</td>
                      <td>{{ $meta->upload_date }}</td>
                      <td>{{ $meta->amount }}</td>
                      <td>{{ $meta->success_amount }}</td>
                      <td>{{ $meta->failure_amount }}</td>
                      <td>{{ $meta->transactions }}</td>
                      <td>{{ $meta->failure }}</td>
                      <td>
                        <button class="btn btn-danger btn-sm" onclick=deleteAxisNach('{{ $meta->id }}','axis') >
                          <i class="fa fa-trash"></i>
                        </button>
                        <a href="{{ route('download.import.file',['importId'=>$meta->id,'bank'=>'axis']) }}">
                        <button class="btn btn-success btn-sm" >
                          <i class="fa fa-download"></i>
                        </button>
                        </a>
                      </td>
                    </tr>
                  @endforeach
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>Month</th>
                    <th>Year</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Success Amount</th>
                    <th>Failure Amount</th>
                    <th>Transactions</th>
                    <th>Failed</th>
                    <th>Action</th>
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
                    <th>Month</th>
                    <th>Year</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Success Amount</th>
                    <th>Failure Amount</th>
                    <th>Transactions</th>
                    <th>Failed</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach(\App\Client\Transaction\YesNachPaymentMeta::all() as $meta)

                    <tr>
                      <td> <a href="{{ route('display.transaction.nach.import.history.details',['importId'=>$meta->id,'bank'=>'yes']) }}">{{ \Carbon\Carbon::parse($meta->upload_date)->format('F') }}                    </a>
                      </td>
                      <td>{{ \Carbon\Carbon::parse($meta->upload_date)->format('Y') }}</td>
                      <td>{{ $meta->upload_date }}</td>
                      <td>{{ $meta->amount }}</td>
                      <td>{{ $meta->success_amount }}</td>
                      <td>{{ $meta->failure_amount }}</td>
                      <td>{{ $meta->transactions }}</td>
                      <td>{{ $meta->failure }}</td>
                      <td>
                        <a href="{{ route('download.import.file',['importId'=>$meta->id,'bank'=>'yes']) }}">
                          <button class="btn btn-success btn-sm" >
                            <i class="fa fa-download"></i>
                          </button>
                        </a>
                      </td>
                    </tr>
                  @endforeach
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>Month</th>
                    <th>Year</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Success Amount</th>
                    <th>Failure Amount</th>
                    <th>Transactions</th>
                    <th>Failed</th>
                    <th>Action</th>
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
  <script>
    function deleteAxisNach(id, bank){
      var confirmation = prompt("Please Type \"CONFIRM\" to delete this File");
      if (confirmation != null) {
        if(confirmation === 'CONFIRM'){
          axios.post('/transaction/import-delete/'+id+'/'+bank)
          .then((response=>{
            if(response.data === 200){
              alert('Transaction Deleted')
              window.location.reload();
            } else {
              alert('OOPS!! Unable to Delete,. Something went wrong.')
            }
          }))
        }
        else{
          alert('Incorrect Phrase. Try Again!')
        }
      }
    }
  </script>
@endsection

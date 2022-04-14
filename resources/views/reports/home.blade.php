@extends('layouts/contentLayoutMaster')

@section('title', ' Reports')

@section('vendor-style')
  {{-- vendor css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
@endsection

@section('content')

  <!-- Zero configuration table -->
  <section id="basic-datatable">

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">Client Summary</div>
          <div class="card-body">
            <table class="table">
              <thead>
              <tr>
                <th>#</th>
                <th>Description</th>
                <th>Value</th>
              </tr>
              </thead>
              <tbody>
              <tr>
                <td>1</td>
                <td>Total Members</td>
                <td><strong>{{ $clients->count() }}</strong></td>
              </tr>
              <tr>
                <td>2</td>
                <td>Total Product Cost</td>
                <td><strong>{{ inr($totalProductCost) }}</strong></td>
              </tr>
              <tr>
                <td>3</td>
                <td>Total Payment Received Till Date</td>
                <td><strong>{{ inr($totalPaymentReceived) }}</strong></td>
              </tr>
              <tr>
                <td>4</td>
                <td>Payment Start Dtae</td>
                <td><strong>{{ \Carbon\Carbon::parse($firstPayment)->format('l, d F, Y') }}</strong></td>
              </tr>
              <tr>
                <td>5</td>
                <td>Last Payment Received Date</td>
                <td><strong>{{ \Carbon\Carbon::parse($latestPayment)->format('l, d F, Y') }}</strong></td>
              </tr>
              <tr>
                <td>6</td>
                <td>Active Members</td>
                <td><strong>{{ $clientStatus['ACTIVE'] }}</strong></td>
              </tr>
              <tr>
                <td>7</td>
                <td>Cancelled Members</td>
                <td><strong>{{ $clientStatus['CANCELLED'] }}</strong></td>
              </tr>
              <tr>
                <td>8</td>
                <td>Breather</td>
                <td><strong>{{ $clientStatus['BREATHER'] }}</strong></td>
              </tr>
              <tr>
              <tr>
                <td>9</td>
                <td>Incomplete</td>
                <td><strong>{{ $clientStatus['INCOMPLETE'] }}</strong></td>
              </tr>
              <tr>
                <td>10</td>
                <td>On Hold</td>
                <td><strong>{{ $clientStatus['ON HOLD'] }}</strong></td>
              </tr>
              <tr>
                <td>11</td>
                <td>Full Payment</td>
                <td><strong>{{ $clientStatus['FULL PAYMENT'] }}</strong></td>
              </tr>
              <tr>
                <td>12</td>
                <td>Active Member Amount</td>
                <td><strong>{{ inr($activeMemberAmount) }}</strong></td>
              </tr>
              <tr>
                <td>13</td>
                <td>Cancelled Member Amount</td>
                <td><strong>{{ inr($cancelledMemberAmount) }}</strong></td>
              </tr>
              <tr>
                <td>14</td>
                <td>Downpayment</td>
                <td><strong>{{ inr($totalDownPayment) }}</strong></td>
              </tr>
              <tr>
                <td>15</td>
                <td>EMI's</td>
                <td><strong>{{ inr($totalEmis) }}</strong></td>
              </tr>

              </tbody>
            </table>

          </div>
        </div>
      </div>
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">Filter Clients</div>
          <form action="" method="get">
            @csrf
            <div class="card-body">
              <div class="row">
                <div class="col-md-4">

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

                  <div id="reportrange2"
                       style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span> <i class="fa fa-caret-down"></i>
                  </div>


                </div>
                <div class="col-md-4">
                  <button class="btn btn-primary" type="submit">Filter</button>
                </div>
                <div class="col-md-4"></div>
              </div>
            </div>
          </form>
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

  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
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

      cb(start, end);
      // cb2(start2, end2);

    });
  </script>

@endsection

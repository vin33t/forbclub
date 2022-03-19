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

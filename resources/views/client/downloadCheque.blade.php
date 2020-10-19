@extends('layouts/contentLayoutMaster')

@section('title', 'Download Cheques')

@section('content')
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
                  <div id="reportrange2" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span> <i class="fa fa-caret-down"></i>
                  </div>
                </div>
              </div>
              <div class="modal-footer" >
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-info">Export</button>
              </div>
            </div>
          </form>

        </div>
      </div>


    </div>
  </div>

@endsection

@section('page-script')
  <!-- Page js files -->
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <script src="{{ asset(mix('js/scripts/modal/components-modal.js')) }}"></script>

  <script>
    $(function() {

      var start = moment().subtract(29, 'days');
      var end = moment();

      function cb(start, end) {
        $('#reportrange2 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        console.log(start.format('YYYY-MM-DD'));
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

    });
  </script>

@endsection

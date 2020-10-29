@extends('layouts/contentLayoutMaster')

@section('title', $client->name)

@section('vendor-style')
  <!-- vendor css files -->
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/jquery.contextMenu.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">

@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
@section('page-style')
  {{-- Page Css files --}}
  <link rel="stylesheet" href="{{ asset(mix('css/plugins/extensions/toastr.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/plugins/extensions/context-menu.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/pages/users.css')) }}">
@endsection
@section('content')
  @if (count($errors) > 0)
    <div class = "alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
  @php
      $user  = \Illuminate\Support\Facades\Auth::user();
  @endphp
  <div id="user-profile">
    <div class="row">
      <div class="col-12">
        <div class="profile-header mb-2">
          <div class="relative">
            <div class="cover-container">
              <img class="img-fluid bg-cover rounded-0 w-100" src="{{ asset('images/profile/user-uploads/cover.jpg') }}"
                   alt="User Profile Image" style="border-radius: 10px !important;">
            </div>
            <div class="profile-img-container d-flex align-items-center justify-content-between">
              <img src="{{ avatar($client->name) }}"
                   class="rounded-circle img-border box-shadow-1" alt="Card image">

              @if($user->employee)
              <div class="float-right">
                <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1" data-toggle="modal" data-target="#editBasicClientDetails">
                  <i class="feather icon-edit-2"></i>
                </button>
                <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary">
                  <i class="feather icon-settings"></i>
                </button>
              </div>
              @endif

            </div>
          </div>
          <div class="d-flex justify-content-end align-items-center profile-header-nav">
            <nav class="navbar navbar-expand-sm w-100 pr-0">
              <button class="navbar-toggler pr-0" type="button" data-toggle="collapse"
                      data-target="navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                      aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"><i class="feather icon-align-justify"></i></span>
              </button>
              <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav justify-content-around w-75 ml-sm-auto">
                  <li class="nav-item px-sm-0">
                    <a href="{{ route('view.client',['slug'=>$client->slug]) }}" class="nav-link font-small-3">Home</a>
                  </li>
                  <li class="nav-item px-sm-0">
                    <a href="{{ route('view.client',['slug'=>$client->slug,'show'=>'payments']) }}" class="nav-link font-small-3" id="client-payment-page">Payments</a>
                  </li>

                  <li class="nav-item px-sm-0">
                    <a href="{{ route('view.client',['slug'=>$client->slug,'show'=>'holidays']) }}" class="nav-link font-small-3">Holidays</a>
                  </li>


                </ul>
              </div>
            </nav>
          </div>
        </div>
      </div>
    </div>
    <section id="profile-info">
      <div class="row">
        @if($client->latestPackage->status == 'Cancelled')
        <div class="col-md-12">
          <div class="alert alert-danger">
            Client Cancelled ({{ $client->latestPackage->remarks == ''? 'CANCELLED DUE TO NON PAYMENT' : $client->latestPackage->remarks }})
          </div>
        </div>
        @endif

        @if(request()->show == 'payments')
        @include('client.components.payments',['client'=>$client])
        @elseif(request()->show == 'holidays')
        @include('client.components.holidays',['client'=>$client])
        @else
          @include('client.components.leftBar',['client'=>$client])
          @include('client.components.timeline',['client'=>$client])
          @include('client.components.rightBar',['client'=>$client])
        @endif
      </div>
{{--      <div class="row">--}}
{{--        <div class="col-12 text-center">--}}
{{--          <button type="button" class="btn btn-primary block-element mb-1">Load More</button>--}}
{{--        </div>--}}
{{--      </div>--}}
    </section>
  </div>


  @if($user->employee)

@include('client.transaction.add.card',['client'=>$client])
@include('client.transaction.add.cash',['client'=>$client])
@include('client.transaction.add.cheque',['client'=>$client])
@include('client.transaction.add.addTransaction',['client'=>$client])
@include('client.transaction.addPdc',['client'=>$client])
@include('client.transaction.add.otherTransaction',['client'=>$client])
@include('client.transaction.disableNach',['client'=>$client])

@include('client.components.editBasic',['client'=>$client])

@endif
@endsection


@section('vendor-script')
  <!-- vendor files -->
  <script src="{{ asset(mix('vendors/js/charts/echarts/echarts.min.js')) }}"></script>

  <script src="{{ asset(mix('vendors/js/extensions/jquery.contextMenu.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/jquery.ui.position.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
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
  <script>

    $('#mode_of_payment').on('change', function(){
      var mode = document.getElementById('mode_of_payment').value;
      console.log('mode');
      if(mode == "Credit Card"){
        var data =  '   <div class="">  '  +
          '   								<label for="card_number">Last Four Digits of Card:  <span style="color:red">*</span></label>  '  +
          '   								<input type="text" name="card_number" maxlength="4" minlength="4" class="form-control" required>  '  +
          '   							</div>  ' ;
        $("#card_details_box").html(data);

      }
      if(mode == "Debit Card"){
        var data =  '   <div class="">  '  +
          '   								<label for="card_number">Last Four Digits of Card:  <span style="color:red">*</span></label>  '  +
          '   								<input type="text" name="card_number" maxlength="4" minlength="4" class="form-control" required>  '  +
          '   							</div>  ' ;
        $("#card_details_box").html(data);

      }
      if(mode == "Cash"){
        var data =  '   <div class="">  '  +
          '   								<label for="cash_receipt_number">Cash Receipt No:  <span style="color:red">*</span></label>  '  +
          '   								<input type="text" name="cash_receipt_no" class="form-control" required>  '  +
          '   							</div>  ' ;
        $("#card_details_box").html(data);

      }

      if(mode == "Cheque"){
        var data =  '   <div class="">  '  +
          '   								<label for="cheque_number">Cheque No.:  <span style="color:red">*</span></label>  '  +
          '   								<input type="text" name="cheque_number" class="form-control" required>  '  +
          '   								<label for="cheque_status">Cheque Status:  <span style="color:red">*</span></label>  '  +
          '            <select name="cheque_status" class="form-control" id="" required>\n' +
          '                <option value="">--SELECT--</option>\n' +
          '                <option value="0">Cleared</option>\n' +
          '                <option value="1">Bounced</option>\n' +
          '            </select>'+
          '   							</div>  ' ;
        $("#card_details_box").html(data);

      }
      if(mode == "NACH"){
        var data =  '   <div class="">  '  +
          '   								<label for="nach_id">NACH ID:  <span style="color:red">*</span></label>  '  +
          '   								<input type="text" name="nach_id" class="form-control" required>  '  +
          '   							</div>  ' ;
        $("#card_details_box").html(data);

      }
      if(mode == ""){
        var data =  '' ;
        $("#card_details_box").html(data);

      }
    });

  </script>

  {{-- Page js files --}}
{{--  <script src="{{ asset(mix('js/scripts/charts/chart-echart.js')) }}"></script>--}}
  <script src="{{ asset(mix('js/scripts/datatables/datatable.js')) }}"></script>

  <script src="{{ asset(mix('js/scripts/pages/user-profile.js')) }}"></script>
{{--  <script src="{{ asset(mix('js/scripts/extensions/context-menu.js')) }}"></script>--}}

  @if($user->employee)

    <script>
    function eventFire(el, etype){
      if (el.fireEvent) {
        el.fireEvent('on' + etype);
      } else {
        var evObj = document.createEvent('Events');
        evObj.initEvent(etype, true, false);
        el.dispatchEvent(evObj);
      }
    }
    $.contextMenu({
      selector: '#user-profile',
      callback: function (key, options) {
        if(key === 'Card'){
          $('#addCardTransaction').modal();
        }
        if(key === 'Cash'){
          $('#addCashTransaction').modal();
        }
        if(key === 'Cheque'){
          $('#addChequeTransaction').modal();
        }
        if(key === 'Other Payments'){
          $('#addOtherTransaction').modal();
        }
        if(key === 'Disable NACH'){
          $('#disableNach').modal();
        }
        if(key === 'Add PDC'){
          $('#addPdc').modal();

        }
        if(key === 'Add Tran'){
          $('#newTransaction').modal();

        }
        if(key === 'View Transactions'){
          window.location.replace("{{ route('view.client',['slug'=>$client->slug,'show'=>'payments']) }}");
        }
        // var r = "Clicked " + key
        // window.console && toastr.success(r);
      },
      items: {
        "Edit Client": { name: "Edit Client" },
        "View Transactions": { name: "View Transactions" },
        "Disable NACH": { name: "Disable NACH" },
        "Add PDC": { name: "Add PDC" },
        // "Add Tran": { name: "Add Tran" },
        "fold1": {
          "name": "Add Transaction",
          "items": {
          "Card": { "name": "Card" },
          "Cash": { "name": "Cash" },
          "Cheque": { "name": "Cheque" },
          "Other Payments": { "name": "Other Payments" },
        }
        }
      }
    })

  </script>
  <script>
    function onlyNumberKey(evt) {

      // Only ASCII charactar in that range allowed
      var ASCIICode = (evt.which) ? evt.which : evt.keyCode
      if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
        return false;
      return true;
    }
  </script>
  @endif

  @if(count($client->transactionSummaryChart))
  <script>

      var pieChart = echarts.init(document.getElementById('transaction-summary-pie-chart'));
      var pieChartoption = {
        tooltip : {
          trigger: 'item',
          formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        legend: {
          orient: 'vertical',
          left: 'left',
          // data: ['Card', 'Cash', 'Cheque', 'NACH', 'Others']
        },
        series : [
          {
            name: 'Amount',
            type: 'pie',
            radius : '55%',
            center: ['50%', '60%'],
            color: ['#FF9F43','#28C76F','#EA5455','#87ceeb','#7367F0'],
            data: {!! $client->transactionSummaryChart !!} ,
            itemStyle: {
              emphasis: {
                shadowBlur: 10,
                shadowOffsetX: 0,
                shadowColor: 'rgba(0, 0, 0, 0.5)'
              }
            }
          }
        ],
      };
      pieChart.setOption(pieChartoption);


  </script>
  @endif
@section('scripts')
  <script>
    function makePayment(id,amount,dateOfPayment){
      $('.paymentMode').html('');
      $('#paymentAmount').html(amount);
      $('#paymentAmount2').val(amount);
      $('#paymentId').val(id);
      $('#dateOfPayment').val(dateOfPayment);
      $('#makePayment').modal();
    }
    $('#modeOfPayment').change(function () {
      $('.paymentMode').html('');
      var mode = this.value;
      console.log(mode);
      if(mode == 'Card'){
        var details = '<div class="col-md-4">\n' +
          '                                    <label for="cardBankName">Card Bank Name</label>\n' +
          '                                    <input type="text" id ="cardBankName" name="cardBankName" class="form-control">\n' +
          '                                </div>\n' +
          '                                <div class="col-md-4">\n' +
          '                                    <label for="cardLastFourDigits">Last Four Digits</label>\n' +
          '                                    <input type="number" id ="cardLastFourDigits" name="cardLastFourDigits" class="form-control">\n' +
          '                                </div>\n' +
          '                                <div class="col-md-4">\n' +
          '                                    <label for="cardDescription">Card Description</label>\n' +
          '                                    <input type="text" id ="cardDescription" name="cardDescription" class="form-control">\n' +
          '                                </div>';
        $('.paymentMode').html(details);
      }
      if(mode == 'Online'){
        var details = '   <div class="col-md-4">\n' +
          '                                <label for="onlineBankName">Bank Name</label>\n' +
          '                                <input type="text" name="bankName" id="onlineBankName" class="form-control">\n' +
          '                            </div>';
        $('.paymentMode').html(details);
      }
      if(mode == 'Bank Transfer'){
        var details = '   <div class="col-md-4">\n' +
          '                                <label for="onlineBankName">Bank Name</label>\n' +
          '                                <input type="text" name="bankName" id="onlineBankName" class="form-control">\n' +
          '                            </div>';
        $('.paymentMode').html(details);
      }
      if(mode == 'Cheque'){
        var details = '<div class="col-md-4">\n' +
          '                                <label for="chequeNumber">Cheque Number</label>\n' +
          '                                <input type="text" name="chequeNumber" id="chequeNumber" class="form-control">\n' +
          '                            </div>';
        $('.paymentMode').html(details);
      }

    });
  </script>


  <script>
    $(document).ready(function() {
      $('.js-example-basic-multiple').select2();
    });
  </script>
  <script>
    function clientHoliday(vin){
      var id  =JSON.parse($(vin).find('.holidayID').val());

    }
  </script>

  <script>
    function editUpcomingTransaction(foo){
      var date_of_payment = $(foo).find('.date_of_payment').val();
      var amount = $(foo).find('.amount').val();
      var id = $(foo).find('.id').val();

      var modal =
        '<div id="modalBhaiHamare2" class="modal fade" role="dialog" style="top:200px !important;">'+
        '<div class="modal-dialog">'+
        '<form method="post" action="">'+
        '@csrf'+
        '<div class="modal-content" style="width:700px !important;">'+
        '<div class="modal-header bg-primary">'+
        '<h5 class="modal-title" id="exampleModalLongTitle" ><strong>Edit Upcoming Transaction</strong></h5>'+
        '</div>'+
        '<div class="modal-body">'+
        '<div class="row">'+
        '<div class="col-md-6">'+
        '<label>Date Of Payment</label>'+
        '<input type="text" value="'+date_of_payment+'" name="date_of_payment" class="form-control">'+
        '<input type="hidden" name="t_id" value="'+id+'">'+
        '</div>'+
        '<div class="col-md-6">'+
        '<label>Amount</label>'+
        '<input type="text" value="'+amount+'" name="amount" class="form-control">'+
        '</div>'+
        '</div>'+
        '</div>'+
        '<div class="modal-footer bg-primary">'+
        '<button type="button" class="btn btn-danger" onclick="$(\'.modal-backdrop\').remove()" data-dismiss="modal">Close</button>'+
        '<button type="submit" class="btn btn-success">Update!</button>'+
        '</div>'+
        '</form>'+
        '</div>'+
        '</div>'+
        '</div>';
      $('#modalDaalo2').html(modal);
      $('#modalBhaiHamare2').modal();
    }

    function editPastTransaction(foo){
      var date_of_payment = $(foo).find('.date_of_payment').val();
      var amount = $(foo).find('.amount').val();
      var mode_of_payment = $(foo).find('.mode_of_payment').val();
      var last_four_card_digits = $(foo).find('.last_four_card_digits').val();
      var card_description = $(foo).find('.card_description').val();
      var bank_name = $(foo).find('.bank_name').val();
      var cheque_number = $(foo).find('.cheque_number').val();
      var id = $(foo).find('.id').val();

      var modal =
        '<div id="modalBhaiHamare3" class="modal fade" role="dialog" style="top:200px !important;">'+
        '<div class="modal-dialog">'+
        '<form method="post" action="">'+
        '@csrf'+
        '<div class="modal-content" style="width:700px !important;">'+
        '<div class="modal-header bg-danger">'+
        '<h5 class="modal-title" id="exampleModalLongTitle" ><strong>Edit Upcoming Transaction</strong></h5>'+
        '</div>'+
        '<div class="modal-body">'+
        '<div class="row">'+
        '<div class="col-md-4">'+
        '<label>Date Of Payment</label>'+
        '<input type="text" value="'+date_of_payment+'" name="date_of_payment" class="form-control">'+
        '<input type="hidden" name="t_id" value="'+id+'">'+
        '</div>'+
        '<div class="col-md-4">'+
        '<label>Amount</label>'+
        '<input type="text" value="'+amount+'" name="amount" class="form-control">'+
        '</div>'+
        '<div class="col-md-4">'+
        '<label>Mode Of Payment</label>'+
        '<input type="text" value="'+mode_of_payment+'" name="mode_of_payment" class="form-control">'+
        '<input type="hidden" name="t_id" value="'+id+'">'+
        '</div>'+
        '</div>'+
        '<div class="row">'+
        '<div class="col-md-3">'+
        '<label>Last Four Card Digits</label>'+
        '<input type="text" value="'+last_four_card_digits+'" name="last_four_card_digits" class="form-control">'+
        '</div>'+
        '<div class="col-md-3">'+
        '<label>Card Description</label>'+
        '<input type="text" value="'+card_description+'" name="card_description" class="form-control">'+
        '</div>'+
        '<div class="col-md-3">'+
        '<label>Bank Name</label>'+
        '<input type="text" value="'+bank_name+'" name="bank_name" class="form-control">'+
        '</div>'+
        '<div class="col-md-3">'+
        '<label>Cheque Number</label>'+
        '<input type="text" value="'+cheque_number+'" name="cheque_number" class="form-control">'+
        '</div>'+
        '</div>'+
        '</div>'+
        '<div class="modal-footer bg-danger">'+
        '<button type="button" class="btn btn-primary" onclick="$(\'.modal-backdrop\').remove()" data-dismiss="modal">Close</button>'+
        '<button type="submit" class="btn btn-success">Update!</button>'+
        '</div>'+
        '</form>'+
        '</div>'+
        '</div>'+
        '</div>';
      $('#modalDaalo2').html(modal);
      $('#modalBhaiHamare3').modal();
    }

    function payUpcoming(foo){
      var date_of_payment = $(foo).find('.date_of_payment').val();
      var amount = $(foo).find('.amount').val();
      var mode_of_payment = $(foo).find('.mode_of_payment').val();
      var last_four_card_digits = $(foo).find('.last_four_card_digits').val();
      var card_description = $(foo).find('.card_description').val();
      var bank_name = $(foo).find('.bank_name').val();
      var cheque_number = $(foo).find('.cheque_number').val();
      var id = $(foo).find('.id').val();

      var modal =
        '<div id="modalBhaiHamare4" class="modal fade" role="dialog" style="top:200px !important;">'+
        '<div class="modal-dialog">'+
        '<form method="post" action="">'+
        '@csrf'+
        '<div class="modal-content" style="width:700px !important;">'+
        '<div class="modal-header bg-success">'+
        '<h5 class="modal-title" id="exampleModalLongTitle" ><strong>Edit Upcoming Transaction</strong></h5>'+
        '</div>'+
        '<div class="modal-body">'+
        '<div class="row">'+
        '<div class="col-md-4">'+
        '<label>Date Of Payment</label>'+
        '<input type="text" value="'+date_of_payment+'" name="date_of_payment" class="form-control">'+
        '<input type="hidden" name="t_id" value="'+id+'">'+
        '</div>'+
        '<div class="col-md-4">'+
        '<label>Amount</label>'+
        '<input type="text" value="'+amount+'" name="amount" class="form-control">'+
        '</div>'+
        '<div class="col-md-4">'+
        '<label>Mode Of Payment</label>'+
        '<input type="text" value="'+mode_of_payment+'" name="mode_of_payment" class="form-control">'+
        '<input type="hidden" name="t_id" value="'+id+'">'+
        '</div>'+
        '</div>'+
        '<div class="row">'+
        '<div class="col-md-3">'+
        '<label>Last Four Card Digits</label>'+
        '<input type="text" value="'+last_four_card_digits+'" name="last_four_card_digits" class="form-control">'+
        '</div>'+
        '<div class="col-md-3">'+
        '<label>Card Description</label>'+
        '<input type="text" value="'+card_description+'" name="card_description" class="form-control">'+
        '</div>'+
        '<div class="col-md-3">'+
        '<label>Bank Name</label>'+
        '<input type="text" value="'+bank_name+'" name="bank_name" class="form-control">'+
        '</div>'+
        '<div class="col-md-3">'+
        '<label>Cheque Number</label>'+
        '<input type="text" value="'+cheque_number+'" name="cheque_number" class="form-control">'+
        '</div>'+
        '</div>'+
        '</div>'+
        '<div class="modal-footer bg-success">'+
        '<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>'+
        '<button type="submit" class="btn btn-warning">Update!</button>'+
        '</div>'+
        '</form>'+
        '</div>'+
        '</div>'+
        '</div>';
      $('#modalDaalo2').html(modal);
      $('#modalBhaiHamare4').modal();

    }
  </script>

@endsection

@extends('layouts/contentLayoutMaster')

@section('title', 'Create Client')
@section('vendor-style')
  <!-- vendor css files -->
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/ui/prism.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/file-uploaders/dropzone.min.css')) }}">
@endsection
@section('page-style')
  <link rel="stylesheet" href="{{ asset(mix('css/plugins/forms/validation/form-validation.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/plugins/file-uploaders/dropzone.css')) }}">
@endsection

@section('content')
  @if (count($errors) > 0)
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
  <form action="{{ route('create.client') }}" method="POST" id="createClientForm" enctype="multipart/form-data">
    @csrf
    <section id="basic-horizontal-layouts">
      <div class="row match-height">
        <div class="col-md-6 col-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Personal Information</h4>
            </div>
            <div class="card-content">
              <div class="card-body">
                <div class="row">
                  <div class="col-12">
                    <div class="form-group row">
                      <div class="col-md-4">
                        <span>Name</span>
                      </div>
                      <div class="col-md-8">
                        <div class="position-relative has-icon-left">
                          <input type="text" id="client-name" class="form-control" name="clientName"
                                 placeholder="Client Name" required
                                 data-validation-required-message="Please Enter a Role Name"
                                 value="{{ old('clientName') }}">
                          <div class="form-control-position">
                            <i class="feather icon-user"></i>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group row">
                      <div class="col-md-4">
                        <span>Email</span>
                      </div>
                      <div class="col-md-8">
                        <div class="position-relative has-icon-left">
                          <input type="email" id="email-id" class="form-control" name="clientEmail"
                                 placeholder="Client Email" value="{{ old('clientEmail') }}">
                          <div class="form-control-position">
                            <i class="feather icon-mail"></i>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group row">
                      <div class="col-md-4">
                        <span>Phone</span>
                      </div>
                      <div class="col-md-8">
                        <div class="position-relative has-icon-left">
                          <input type="number" id="contact-info" class="form-control" name="clientPhone"
                                 placeholder="Client Phone Number" required>
                          <div class="form-control-position">
                            <i class="feather icon-smartphone"></i>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group row">
                      <div class="col-md-4">
                        <span>Alternate Phone</span>
                      </div>
                      <div class="col-md-8">
                        <div class="position-relative has-icon-left">
                          <input type="number" id="contact-info" class="form-control" name="clientAltPhone"
                                 placeholder="Client Alternate Phone Number">
                          <div class="form-control-position">
                            <i class="feather icon-smartphone"></i>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group row">
                      <div class="col-md-4">
                        <span>Date Of Birth</span>
                      </div>
                      <div class="col-md-8">
                        <input type="date" id="birthDate" class="form-control" name="clientBirthDate"
                               placeholder="Date of Birth">
                      </div>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group row">
                      <div class="col-md-4">
                        <span>Address</span>
                      </div>
                      <div class="col-md-8">
                        <input type="text" id="birthDate" class="form-control" name="address" placeholder="Address">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Enrollment Information</h4>
            </div>
            <div class="card-content">
              <div class="card-body">
                <div class="form-body">
                  <div class="row">
                    <div class="col-12">
                      <div class="form-group row">
                        <div class="col-md-4">
                          <span>Date Of Enrollment</span>
                        </div>
                        <div class="col-md-8">
                          <div class="position-relative has-icon-left">
                            <input type="date" class="form-control" name="productEnrollmentDate"
                                   placeholder="Date of Enrollment" required>
                            <div class="form-control-position">
                              <i class="feather icon-calendar"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group row">
                        <div class="col-md-4">
                          <span>Maf No</span>
                        </div>
                        <div class="col-md-8">
                          <div class="position-relative has-icon-left">
                            <input type="text" id="email-icon" class="form-control" name="productMafNo"
                                   placeholder="Maf No" required>
                            <div class="form-control-position">
                              <i class="feather icon-hash"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group row">
                        <div class="col-md-4">
                          <span>Fclp Id</span>
                        </div>
                        <div class="col-md-8">
                          <div class="position-relative has-icon-left">
                            <input type="number" id="contact-icon" class="form-control" name="productFclpId"
                                   placeholder="Fclp Id" required>
                            <div class="form-control-position">
                              <i class="feather icon-hash"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group row">
                        <div class="col-md-4">
                          <span>Sale By</span>
                        </div>
                        <div class="col-md-8">
                          <div class="position-relative has-icon-left">
                            <input type="text" id="pass-icon" class="form-control" name="productSaleBy"
                                   placeholder="Sale By" required>
                            <div class="form-control-position">
                              <i class="feather icon-user"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group row">
                        <div class="col-md-4">
                          <span>Sale Manager</span>
                        </div>
                        <div class="col-md-8">
                          <div class="position-relative has-icon-left">
                            <input type="text" id="pass-icon" class="form-control" name="productSaleManager"
                                   placeholder="Sale Manager" required>
                            <div class="form-control-position">
                              <i class="feather icon-user"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group row">
                        <div class="col-md-4">
                          <span>Branch</span>
                        </div>
                        <div class="col-md-8">
                          <div class="position-relative has-icon-left">
                            {{--                            <input type="text" id="pass-icon" class="form-control" name="productBranch" placeholder="Branch" required>--}}
                            <select name="productBranch" id="pass-icon" required class="form-control">
                              <option value="">--Select--</option>
                              <option value="CHANDIGARH">Chandigarh</option>
                              <option value="DELHI">Delhi</option>
                            </select>
                            <div class="form-control-position">
                              <i class="feather icon-home"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section id="multiple-column-form">
      <div class="row match-height">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Co Applicant Details</h4>
            </div>
            <div class="card-content">
              <div class="card-body">
                <div class="form-body">
                  <div class="row">
                    <div class="col-md-6 col-12">
                      <div class="form-group">
                        <label for="product-type">Date Of Birth</label>
                        <input type="date" id="product-name" class="form-control" name="coDob">

                      </div>
                    </div>
                    <div class="col-md-6 col-12">
                      <div class="form-group">
                        <label for="product-name">Relationship With Client</label>
                        <input type="text" id="product-name" class="form-control" placeholder="Relationship With Client"
                               name="relationShipWithClient">
                      </div>
                    </div>
                    <div class="col-md-6 col-12">
                      <div class="form-label-group">
                        <input type="text" id="product-tenure" class="form-control" placeholder="Co Applicant Name"
                               name="coApplicantName">
                        <label for="product-tenure">Co Applicant Name</label>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Product Information</h4>
            </div>
            <div class="card-content">
              <div class="card-body">
                <div class="form-body">
                  <div class="row">
                    <div class="col-md-6 col-12">
                      <div class="form-group">
                        <label for="product-type">Product Type</label>
                        <select id="product-type" class="select2 form-control" name="productType" required>
                          <option value="fclp">FCLP</option>
                          <option value="fcv">FCV</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6 col-12">
                      <div class="form-group">
                        <label for="product-name">Product Name</label>
                        {{--                        <input type="text" id="product-name" class="form-control" placeholder="Product Name" name="productName" required>--}}
                        <select name="productName" id="product-name" class="form-control" required>
                          <option value="">--SELECT--</option>
                          <option value="Exclusive India">Exclusive India</option>
                          <option value="Classic">Classic</option>
                          <option value="Imperia">Imperia</option>
                          <option value="Luxe">Luxe</option>
                          <option value="Royale">Royale</option>
                          <option value="Classic FCV">Classic FCV</option>
                          <option value="India FCV">India FCV</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6 col-12">
                      <div class="form-label-group">
                        <input type="number" id="product-tenure" class="form-control" placeholder="Product Tenure"
                               name="productTenure" required>
                        <label for="product-tenure">Product Tenure</label>
                      </div>
                    </div>
                    <div class="col-md-6 col-12">
                      <div class="form-label-group">
                        <input type="number" id="product-cost" class="form-control" name="productCost"
                               placeholder="Product Cost" required>
                        <label for="product-cost">Product Cost</label>
                      </div>
                    </div>
                    <div class="col-md-6 col-12">
                      <div class="form-label-group">
                        <input type="number" id="product-tenure" class="form-control" placeholder="Number Of EMI"
                               name="noOfEmi" required>
                        <label for="product-tenure">Number Of EMI</label>
                      </div>
                    </div>
                    <div class="col-md-6 col-12">
                      <div class="form-label-group">
                        <input type="number" id="product-cost" class="form-control" name="emiAmount"
                               placeholder="EMI Amount" required>
                        <label for="product-cost">EMI Amount</label>
                      </div>
                    </div>
                    <div class="col-md-6 col-12">
                      <div class="form-label-group">
                        <input type="number" id="product-cost" class="form-control" name="dpAmount"
                               placeholder="DP Amount" required>
                        <label for="product-cost">DP Amount</label>
                      </div>
                    </div>       <div class="col-md-6 col-12">
                      <div class="form-label-group">
                        <input type="number" id="product-cost" class="form-control" name="asc"
                               placeholder="Annual Service Charges(ASC)">
                        <label for="product-cost">Annual Service Charges</label>
                      </div>
                    </div>
                    <div class="col-md-4 col-12">
                      <div class="form-label-group">
                        <select name="productModeOfPayment" id="product_mode_of_payment" class="form-control"
                                required="">
                          <option value="">---Select Mode Of Payment---</option>
                          <option value="Credit Card">Credit Card</option>
                          <option value="Debit Card">Debit Card</option>
                          <option value="Cash">Cash</option>
                          <option value="Cheque">Cheque</option>
                          <option value="Paytm">Paytm</option>
                          <option value="Online">Online</option>
                        </select>
                        <label for="product_mode_of_payment">Mode Of Payment </label>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <a href="javascript:void(0)" id="add_mode_of_payment" class="" style="margin-top:20px;"><i
                          class="fa fa-plus"></i></a>
                      <a href="javascript:void(0)" id="remove_mode_of_payment" class=""
                         style="margin-top:20px;display:none;"><i class="fa fa-minus"></i></a>
                    </div>
                    <div class="col-md-12 col-12">
                      <div class="row" id="card_details_box">

                      </div>
                    </div>

                    <div id="show_mode_of_payment" class="col-md-3" style="display:none;">

                    </div>
                    <div id="show_mode_of_payment_amount" class="col-md-3" style="display:none;">
                    </div>
                    <div class="col-md-12 col-12">
                      <div class="row" id="card_details_box_two">


                      </div>
                    </div>
                    <hr>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              {{--            <h4 class="card-title">Package Benefits <button class="btn btn-primary btn-sm" onclick="addMoreBenefit()" type="button">Add More</button></h4>--}}
              <h4 class="card-title">Package Benefits  <button type="button" class="btn btn-success btn-sm" onclick="addMoreBenefit()"><i class="fa fa-plus"></i></button></h4>
            </div>
            <div class="card-content">
              <div class="card-body">

                <div id="moreBenefits">

                </div>


              </div>
            </div>
          </div>
        </div>
      </div>


      <div class="row">
        <div class="col-12">
          <div class="card card-box">
            <div class="card-head">
              <header>Acocunt Status</header>
            </div>
            <div class="card-body no-padding height-9">
              <div class="row">
                <div class="col-md-2">
                  <label for="nach">NACH: </label><br>
                  <input type="radio" name="nach_a" value="0">No
                  <input type="radio" name="nach_a" value="1">Yes
                  {{-- <input type="checkbox" class="form" id="nach" name="nach" value="1" @if($details->activationStatus) @if($details->activationStatus->nach == 1) checked @endif @endif> --}}
                </div>
                {{--              <div class="col-md-2">--}}
                {{--                <label for="">PDC</label> <span onclick="$('input[name=pdc_a]').attr('checked',false);"><i class="fa fa-refresh" aria-hidden="true"></i></span><br>--}}
                {{--                <input type="radio" name="pdc_a" class="from-control"  value="1" >1 PDC--}}
                {{--                <input type="radio" name="pdc_a" class="from-control" value="2" >2 PDC--}}
                {{--              </div>--}}
                <div class="col-md-2">
                  <input type="checkbox" class="from-control" name="idProof_a" value="1"> <label>ID Proof</label>
                </div>
                <div class="col-md-2">
                  <input type="checkbox" class="from-control" name="tc_a" value="1"> <label>T&C</label>
                </div>
                <div class="col-md-2">
                  <input type="checkbox" class="from-control" name="cancelledCheque_a" value="1"> <label>Cancelled
                    Cheque</label>
                </div>
                <div class="col-md-2">
                  <input type="checkbox" class="" name="maf_a" value="1"> <label>MAF</label>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>


      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">File Upload</h4>
            </div>
            <div class="card-content">
              <div class="card-body">
                <div class="row">
                  <div class="col-lg-6 col-md-12">
                    <fieldset class="form-group">
                      <label for="basicInputFile">MAF</label>
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" name="clientMaf" id="inputGroupFile01">
                        <label class="custom-file-label" for="inputGroupFile01">Choose MAF File</label>
                      </div>
                    </fieldset>
                  </div>
                  <div class="col-lg-6 col-md-12">
                    <fieldset class="form-group">
                      <label for="basicInputFile">Photo</label>
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="inputGroupFile01">
                        <label class="custom-file-label" for="inputGroupFile01">Choose Enrollment Photo</label>
                      </div>
                    </fieldset>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <div class="row">
      <div class="col-md-12 text-right">
        <div class="card">
          <div class="card-body">
            <button type="submit" class="btn bg-gradient-success mr-1 mb-1">Save Client</button>
          </div>
        </div>
      </div>
    </div>
  </form>
@endsection
@section('vendor-script')
  <!-- vendor files -->
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/validation/jqBootstrapValidation.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/dropzone.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/ui/prism.min.js')) }}"></script>
@endsection
@section('page-script')
  <!-- Page js files -->
  <script src="{{ asset(mix('js/scripts/forms/select/form-select2.js')) }}"></script>
  <script src="{{ asset(mix('js/scripts/extensions/dropzone.js')) }}"></script>
  <script>
    $(document).ready(function () {
      $("#add_mode_of_payment").click(function () {
        var append = '<label for="fclp_mode_of_payment"> Second Mode of Payment </label>' +
          '<select name="productModeOfPaymentTwo" id="productModeOfPaymentTwo" class="form-control" required onChange="mode(this.value)">' +
          '                          <option value="">---Select Mode Of Payment---</option>\n' +
          '                          <option value="Credit Card">Credit Card</option>\n' +
          '                          <option value="Debit Card">Debit Card</option>\n' +
          '                          <option value="Cash">Cash</option>\n' +
          '                          <option value="Cheque">Cheque</option>\n' +
          '                          <option value="Paytm">Paytm</option>\n' +
          '                          <option value="Online">Online</option>\n' +
          '                        </select>'
          '</select>';
        var amount = '<label for="amount">Amount</label>' +
          '<input type="number" name="amount_two" class="form-control" required>'
        $("#show_mode_of_payment").html(append);
        $("#show_mode_of_payment_amount").html(amount);
        document.getElementById("show_mode_of_payment").style.display = "block";
        document.getElementById("show_mode_of_payment_amount").style.display = "block";
        document.getElementById("add_mode_of_payment").style.display = "none";
        document.getElementById("remove_mode_of_payment").style.display = "block";
      });
      $("#remove_mode_of_payment").click(function () {
        $("#show_mode_of_payment").hide();
        $("#show_mode_of_payment").html('');
        $("#show_mode_of_payment_amount").hide();
        $("#show_mode_of_payment_amount").html('');
        document.getElementById("add_mode_of_payment").style.display = "block";
        document.getElementById("remove_mode_of_payment").style.display = "none";
      })
    });
  </script>
  <script>
    function addMoreBenefit() {
      console.log('asd');
    }

    $(document).ready(function () {
      $('#product_mode_of_payment').on('change', function () {
        var mode = document.getElementById('product_mode_of_payment').value;
        console.log(mode);
        if (mode == "Credit Card") {
          var data = '   <div class="col-md-4">  ' +
            '   								<label for="fclp_card_number">Last Four Digits of Card:  <span style="color:red">*</span></label>  ' +
            '   								<input type="text" name="fclp_card_number_one" maxlength="4" minlength="4" class="form-control" required>  ' +
            '   							</div>  ' +
            '   							<div class="col-md-4">  ' +
            '   								<label for="fclp_date"> Date of Payment:  <span style="color:red">*</span></label>  ' +
            '   								<input type="date" name="fclp_date_one" value="" placeholder="DD/MM/YYYY" class="form-control"  id="datepicker2" required>  ' +
            '   							</div>  ' +
            '   							<div class="col-md-4" >  ' +
            '   								<label for="fclp_card_type"> Card Type:  <span style="color:red">*</span></label>  ' +
            '      <select name="fclp_card_type_one" id="" class="form-control" required="">' +
            '        <option value="">--Select--</option>' +
            '        <option value="Visa">Visa</option>' +
            '        <option value="Masters">Masters</option>' +
            '        <option value="Diners">Diners</option>' +
            '        <option value="Amex">Amex</option>' +
            '        <option value="Rupay">Rupay</option>' +
            '        <option value="Maestro">Maestro</option>' +
            '      </select>' +
            '   							</div>  ' +
            '   							<div class="col-md-4" style="diplay: none" >  ' +
            '   									<label for="fclp_card_issue_bank_name">Card Issue Bank Name:  <span style="color:red">*</span></label>  ' +
            '   									<input type="text" name="fclp_card_issue_bank_name_one" class="form-control" required value="na">  ' +
            '  								</div>  ' +
            '								<div class="col-md-4">  ' +
            '   								<label for="remarks">Remarks ' +
            '   								<input type="text" name="remarks_one" value="" class="form-control" required>  ' +
            '   							</div>  ';
          $("#card_details_box").html(data);

        }
        if (mode == "Debit Card") {
          var data = '   <div class="col-md-4">  ' +
            '   								<label for="fclp_card_number">Last Four Digits of Card:  <span style="color:red">*</span></label>  ' +
            '   								<input type="text" name="fclp_card_number_one" maxlength="4" minlength="4" class="form-control" required>  ' +
            '   							</div>  ' +
            '   							<div class="col-md-4">  ' +
            '   								<label for="fclp_date"> Date of Payment:  <span style="color:red">*</span></label>  ' +
            '   								<input type="date" name="fclp_date_one" value="" placeholder="DD/MM/YYYY" class="form-control"  id="datepicker2" required>  ' +
            '   							</div>  ' +
            '   							<div class="col-md-4" >  ' +
            '   								<label for="fclp_card_type"> Card Type:  <span style="color:red">*</span></label>  ' +
            '      <select name="fclp_card_type_one" id="" class="form-control" required="">' +
            '        <option value="">--Select--</option>' +
            '        <option value="Visa">Visa</option>' +
            '        <option value="Masters">Masters</option>' +
            '        <option value="Diners">Diners</option>' +
            '        <option value="Amex">Amex</option>' +
            '        <option value="Rupay">Rupay</option>' +
            '        <option value="Maestro">Maestro</option>' +
            '      </select>' +
            '   							</div>  ' +
            '   								<div class="col-md-4">  ' +
            '   								<label for="remarks">Remarks  ' +
            '   								<input type="text" name="remarks_one" value="" class="form-control" required>  ' +
            '   							</div>  ';
          $("#card_details_box").html(data);

        }
        if (mode == "Cheque") {
          var data = '   <div class="col-md-4">  ' +
            '   								<label for="cheque_no">Cheque Number:<span style="color:red">*</span></label>  ' +
            '   								<input type="text" name="cheque_no_one" class="form-control" required>  ' +
            '   							</div>  ' +
            '   								<div class="col-md-4">  ' +
            '   								<label for="paymnet_date">Date<span style="color:red">*</span></label>  ' +
            '   								<input type="date" name="fclp_date_one" value="{{Carbon\Carbon::now()->toDateString()}}" class="form-control" required>  ' +
            '   							</div>  ' +
            '   								<div class="col-md-4">  ' +
            '   								<label for="remarks">Remarks ' +
            '   								<input type="text" name="remarks_one" value="" class="form-control" required>  ' +
            '   							</div>  ';
          $("#card_details_box").html(data);
        }
        if (mode == "Cash") {
          var data = '   <div class="col-md-4">  ' +
            '   								<label for="cash_receipt_no">Cash Receipt No:<span style="color:red">*</span></label>  ' +
            '   								<input type="text" name="cash_receipt_no_one" class="form-control" required>  ' +
            '   							</div>  ' +
            '   								<div class="col-md-4">  ' +
            '   								<label for="paymnet_date">Date<span style="color:red">*</span></label>  ' +
            '   								<input type="date" name="fclp_date_one" value="{{Carbon\Carbon::now()->toDateString()}}" class="form-control" required>  ' +
            '   							</div>  ' +
            '   								<div class="col-md-4">  ' +
            '   								<label for="remarks">Remarks  ' +
            '   								<input type="text" name="remarks_one" value="" class="form-control" required>  ' +
            '   							</div>  ';
          $("#card_details_box").html(data);
        }
        if (mode == "") {
          var data = "";
          $("#card_details_box").html(data);
        }

        if (mode == "Online") {
          var data = '   								<div class="col-md-4">  ' +
            '   								<label for="remarks">Remarks  ' +
            '   								<input type="text" name="remarks_one" value="" class="form-control" required>  ' +
            '   							</div>  ';
          $("#card_details_box").html(data);
        }

        if (mode == "Paytm") {
          var data = '   								<div class="col-md-4">  ' +
            '   								<label for="remarks">Remarks  ' +
            '   								<input type="text" name="remarks_one" value="" class="form-control" required>  ' +
            '   							</div>  ';
          $("#card_details_box").html(data);
        }

      });
    });
    function mode(temp){
      if(temp == "Credit Card"){
        var data =  '   <div class="col-md-4">  '  +
          '   								<label for="fclp_card_number">Last Four Digits of Card:  <span style="color:red">*</span></label>  '  +
          '   								<input type="text" name="fclp_card_number_two" maxlength="4" minlength="4" class="form-control" required>  '  +
          '   							</div>  '  +
          '   							<div class="col-md-4">  '  +
          '   								<label for="fclp_date"> Date of Payment:  <span style="color:red">*</span></label>  '  +
          '   								<input type="date" name="fclp_date_two" value="" placeholder="DD/MM/YYYY" class="form-control"  id="datepicker2" required>  '  +
          '   							</div>  '  +
          '   							<div class="col-md-4" >  ' +
          '   								<label for="fclp_card_type"> Card Type:  <span style="color:red">*</span></label>  ' +
          '      <select name="fclp_card_type_two" id="" class="form-control" required="">' +
          '        <option value="">--Select--</option>' +
          '        <option value="Visa">Visa</option>' +
          '        <option value="Masters">Masters</option>' +
          '        <option value="Diners">Diners</option>' +
          '        <option value="Amex">Amex</option>' +
          '        <option value="Rupay">Rupay</option>' +
          '        <option value="Maestro">Maestro</option>' +
          '      </select>' +
          '   							</div>  ' +
          '								<div class="col-md-4">  '  +
          '   								<label for="remarks">Remarks '  +
          '   								<input type="text" name="remarks_two" value="" class="form-control" remarks>  '  +
          '   							</div>  ' ;
        $("#card_details_box_two").html(data);

      }
      if(temp == "Debit Card"){
        var data =  '<div class="col-md-4">  '  +
          '   								<label for="fclp_card_number">Last Four Digits of Card:  <span style="color:red">*</span></label>  '  +
          '   								<input type="text" name="fclp_card_number_two" maxlength="4" minlength="4" class="form-control" required>  '  +
          '   							</div>  '  +
          '   							<div class="col-md-4">  '  +
          '   								<label for="fclp_date"> Date of Payment:  <span style="color:red">*</span></label>  '  +
          '   								<input type="date" name="fclp_date_two" value="" placeholder="DD/MM/YYYY" class="form-control"  id="datepicker2" required>  '  +
          '   							</div>  '  +
          '   							<div class="col-md-4" >  ' +
          '   								<label for="fclp_card_type"> Card Type:  <span style="color:red">*</span></label>  ' +
          '      <select name="fclp_card_type_two" id="" class="form-control" required="">' +
          '        <option value="">--Select--</option>' +
          '        <option value="Visa">Visa</option>' +
          '        <option value="Masters">Masters</option>' +
          '        <option value="Diners">Diners</option>' +
          '        <option value="Amex">Amex</option>' +
          '        <option value="Rupay">Rupay</option>' +
          '        <option value="Maestro">Maestro</option>' +
          '      </select>' +
          '   							</div>  ' +
          '   								<div class="col-md-4">  '  +
          '   								<label for="remarks">Remarks  '  +
          '   								<input type="text" name="remarks_two" value="" class="form-control" required>  '  +
          '   							</div>  ' ;
        $("#card_details_box_two").html(data);

      }
      if(temp == "Cheque"){
        var data = '   <div class="col-md-4">  '  +
          '   								<label for="cheque_no">Cheque Number:<span style="color:red">*</span></label>  '  +
          '   								<input type="text" name="cheque_no_two" class="form-control" required>  '  +
          '   							</div>  '  +
          '   								<div class="col-md-4">  '  +
          '   								<label for="paymnet_date">Date<span style="color:red">*</span></label>  '  +
          '   								<input type="date" name="fclp_date_two" value="{{Carbon\Carbon::now()->toDateString()}}" class="form-control" required>  '  +
          '   							</div>  ' +
          '   								<div class="col-md-4">  '  +
          '   								<label for="remarks">Remarks '  +
          '   								<input type="text" name="remarks_two" value="" class="form-control" required>  '  +
          '   							</div>  ' ;
        $("#card_details_box_two").html(data);
      }
      if(temp == "Cash"){
        var data = '   <div class="col-md-4">  '  +
          '   								<label for="cash_receipt_no">Cash Receipt No:<span style="color:red">*</span></label>  '  +
          '   								<input type="text" name="cash_receipt_no_two" class="form-control" required>  '  +
          '   							</div>  '  +
          '   								<div class="col-md-4">  '  +
          '   								<label for="paymnet_date">Date<span style="color:red">*</span></label>  '  +
          '   								<input type="date" name="fclp_date_two" value="{{Carbon\Carbon::now()->toDateString()}}" class="form-control" required>  '  +
          '   							</div>  ' +
          '   								<div class="col-md-4">  '  +
          '   								<label for="remarks">Remarks  '  +
          '   								<input type="text" name="remarks_two" value="" class="form-control" required>  '  +
          '   							</div>  ' ;
        $("#card_details_box_two").html(data);
      }
      if(temp == ""){
        var data = "";
        $("#card_details_box_two").html(data);
      }

      if(temp =="Online" || temp == "Paytm" ){
        var data = '   								<div class="col-md-4">  ' +
          '   								<label for="remarks">Remarks  ' +
          '   								<input type="text" name="remarks_two" value="" class="form-control" required>  ' +
          '   							</div>  ';
        $("#card_details_box_two").html(data);
      }
    }


    function addMoreBenefit(){
      var data = '<div class="row remove">\n' +
        '                  <div class="col-md-5  col-sm-12">\n' +
        '                    <div class="form-label-group">\n' +
        '                      <select name="benefitName[]" id="" class="form-control">\n' +
        '                        <option value="Flight">Flight</option>\n' +
        '                        <option value="Hotel">Hotel</option>\n' +
        '                        <option value="Land Package">Land Package</option>\n' +
        '                        <option value="Others">Others</option>\n' +
        '                      </select>\n' +
        '                    </div>\n' +
        '                  </div>\n' +
        '                  <div class="col-md-5 col-sm-12">\n' +
        '                    <div class="form-label-group">\n' +
        '                      <input type="text" placeholder="Benefit Description" class="form-control"\n' +
        '                             name="benefitDescription[]">\n' +
        '                      <label>Benefit Description </label>\n' +
        '                    </div>\n' +
        '                  </div>\n' +
        '                  <div class="col-md-2">\n' +
        '                    <button type="button" class="btn btn-danger btn-sm" onclick="remove(this)"><i class="fa fa-minus"></i></button>\n' +
        '                  </div>\n' +
        '\n' +
        '                </div>';
      $('#moreBenefits').append(data);
    }
    function remove(foo){
      $(foo).parents(".remove").remove();
    }
  </script>


@endsection


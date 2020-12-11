@extends('layouts/contentLayoutMaster')

@section('title',  'Summary')

@section('page-style')
  {{-- Page Css files --}}
  <link rel="stylesheet" href="{{ asset(mix('css/pages/invoice.css')) }}">
@endsection
@section('content')
  <!-- invoice functionality start -->
  <section class="invoice-print mb-1">
    <div class="row">
      <fieldset class="col-12 col-md-5 mb-1 mb-md-0">
{{--        <div class="input-group">--}}
{{--          <input type="text" class="form-control" placeholder="Email" aria-describedby="button-addon2">--}}
{{--          <div class="input-group-append" id="button-addon2">--}}
{{--            <button class="btn btn-outline-primary" type="button">Send Invoice</button>--}}
{{--          </div>--}}
{{--        </div>--}}
      </fieldset>
      <div class="col-12 col-md-7 d-flex flex-column flex-md-row justify-content-end">
        <button class="btn btn-primary btn-print mb-1 mb-md-0"> <i class="feather icon-file-text"></i> Print</button>
{{--        <button class="btn btn-outline-primary  ml-0 ml-md-1"> <i class="feather icon-download"></i> Download</button>--}}
      </div>
    </div>
  </section>
  <!-- invoice functionality end -->
  @php
    $package = $client->latestPackage;
  @endphp
  <section class="card invoice-page">
    <div id="invoice-template" class="card-body">
      <!-- Invoice Company Details -->
      <div id="invoice-company-details" class="row">
        <div class="col-md-6 col-sm-12 text-left pt-1">
          <div class="media pt-1">
            <img src="https://www.forbclub.com/images/logo.png" alt="company logo" class="" width="200px"/>
          </div>
          <hr>
          <h6>Client Name</h6>
          <p>{{ $client->name }}</p>
          @if($client->phone)
          <h6 class="mt-2">Phone</h6>
           <p>{{ $client->phone }}</p>
          @endif
          @if($client->email)
            <h6 class="mt-2">Email</h6>
            <p>{{ $client->email }}</p>
          @endif
          <h6 class="mt-2">Product</h6>
          <p>{{ $package->productType }} | {{ $package->productName }} | {{ $package->productTenure }} Years | {{ inr($package->productCost) }}</p>
          <h6 class="mt-2">Fully Paid Holidays</h6>
          <p>
            @if($client->latestPackage->productName == 'Classic FCV' or $client->latestPackage->productName == 'India FCV') N/A @else 1{5N/6D 02 Adults} @endif

          </p>
        </div>
        <div class="col-md-6 col-sm-12 text-right">
          <h1>Summary</h1>
          <div class="invoice-details mt-2">

            <h6>FTK ID | MAF NO</h6>
            <p>{{ $package->fclpId }} | {{ $package->mafNo }}</p>
            <h6 class="mt-2">Enrollment Date</h6>
            <p>{{ \Carbon\Carbon::parse($package->enrollmentDate)->format('d M, Y') }}</p>
            <h6 class="mt-2">Membership Expiring On</h6>
            <p>{{  \Carbon\Carbon::parse($package->enrollmentDate)->addYears($package->productTenure)->format('d M, Y') }}</p>
          <h6 class="mt-2">EMI Start Date - EMI End Date</h6>
            <p>
              {{  \Carbon\Carbon::parse($package->enrollmentDate)->addMonths(1)->startOfMonth()->addDays(4)->format('d M, Y') }} -
              @php
                if($client->emiRegularPlan) {
                        $emiNo = $client->emiRegularPlan;
                    }
                 else{
                   $emiNo = $client->latestPackage->noOfEmi;
                 }
              @endphp
              {{  \Carbon\Carbon::parse($package->enrollmentDate)->addMonths($emiNo)->startOfMonth()->addDays(4)->format('d M, Y') }}
            </p>
          </div>
        </div>
      </div>
      <!--/ Invoice Company Details -->

      <!-- Invoice Recipient Details -->
{{--      <div id="invoice-customer-details" class="row pt-2">--}}
{{--        <div class="col-sm-6 col-12 text-left">--}}
{{--          <h5>Recipient</h5>--}}
{{--          <div class="recipient-info my-2">--}}
{{--            <p>Peter Stark</p>--}}
{{--            <p>8577 West West Drive</p>--}}
{{--            <p>Holbrook, NY</p>--}}
{{--            <p>90001</p>--}}
{{--          </div>--}}
{{--          <div class="recipient-contact pb-2">--}}
{{--            <p>--}}
{{--              <i class="feather icon-mail"></i>--}}
{{--              peter@mail.com--}}
{{--            </p>--}}
{{--            <p>--}}
{{--              <i class="feather icon-phone"></i>--}}
{{--              +91 988 888 8888--}}
{{--            </p>--}}
{{--          </div>--}}
{{--        </div>--}}
{{--        <div class="col-sm-6 col-12 text-right">--}}
{{--          <h5>Microsion Technologies Pvt. Ltd.</h5>--}}
{{--          <div class="company-info my-2">--}}
{{--            <p>9 N. Sherwood Court</p>--}}
{{--            <p>Elyria, OH</p>--}}
{{--            <p>94203</p>--}}
{{--          </div>--}}
{{--          <div class="company-contact">--}}
{{--            <p>--}}
{{--              <i class="feather icon-mail"></i>--}}
{{--              hello@pixinvent.net--}}
{{--            </p>--}}
{{--            <p>--}}
{{--              <i class="feather icon-phone"></i>--}}
{{--              +91 999 999 9999--}}
{{--            </p>--}}
{{--          </div>--}}
{{--        </div>--}}
{{--      </div>--}}
      <!--/ Invoice Recipient Details -->
    @php
      $totalTransactions = collect();
        if($client->cashPayments->count()){
            foreach($client->CashPayments as $ca){
              $totalTransactions->push(['date'=>$ca->paymentDate,'amount'=>$ca->amount,'remarks'=>$ca->remarks,'mode'=>'Cash','dp'=>$ca->isDp]);
            }
        }
        if($client->cardPayments->count()){
            foreach($client->CardPayments as $cad){
              $totalTransactions->push(['date'=>$cad->paymentDate,'amount'=>$cad->amount,'remarks'=>$cad->remarks,'mode'=>'Card','dp'=>$cad->isDp]);
            }
        }
        if($client->chequePayments->count()){
            foreach($client->chequePayments as $che){
              $totalTransactions->push(['date'=>$che->paymentDate,'amount'=>$che->amount,'remarks'=>$che->remarks,'mode'=>'Cheque','dp'=>$che->isDp]);
            }
        }
        if($client->otherPayments->count()){
            foreach($client->otherPayments as $oth){
              $totalTransactions->push(['date'=>$oth->paymentDate,'amount'=>$oth->amount,'remarks'=>$oth->remarks,'mode'=>$oth->modeOfPayment,'dp'=>$oth->isDp]);
            }
        }
        if($client->AxisPayments->count()){
            foreach($client->AxisPayments as $axp){
              if($axp->status_description == 'success' or $axp->status_description == 'SUCCESS' or $axp->status_description == 'Success'){
                $totalTransactions->push(['date'=>$axp->date_of_transaction,'amount'=>$axp->amount,'remarks'=>$axp->reason_description,'mode'=>'AXIS NACH','dp'=>'']);
              }
            }
        }

        if($client->YesPayments->count()){
            foreach($client->YesPayments as $yep){
              if($yep->STATUS == 'ACCEPTED'){
                $totalTransactions->push(['date'=>$yep->VALUE_DATE,'amount'=>$yep->AMOUNT,'remarks'=>$yep->REASON_CODE,'mode'=>'YES NACH','dp'=>'']);
              }
            }
        }


    $addOnTransactions = collect();
  if($client->cashPayments->count()){
  foreach($client->CashPayments->where('isAddon', 1) as $ca){
  $addOnTransactions->push(['date'=>$ca->paymentDate,'amount'=>$ca->amount,'remarks'=>$ca->remarks,'mode'=>'Cash','dp'=>$ca->isDp]);
  }
  }
  if($client->cardPayments->count()){
  foreach($client->CardPayments->where('isAddon', 1) as $cad){
  $addOnTransactions->push(['date'=>$cad->paymentDate,'amount'=>$cad->amount,'remarks'=>$cad->remarks,'mode'=>'Card','dp'=>$cad->isDp]);
  }
  }
  if($client->chequePayments->count()){
  foreach($client->chequePayments->where('isAddon', 1) as $che){
  $addOnTransactions->push(['date'=>$che->paymentDate,'amount'=>$che->amount,'remarks'=>$che->remarks,'mode'=>'Cheque','dp'=>$che->isDp]);
  }
  }
  if($client->otherPayments->count()){
  foreach($client->otherPayments->where('isAddon', 1) as $oth){
  $addOnTransactions->push(['date'=>$oth->paymentDate,'amount'=>$oth->amount,'remarks'=>$oth->remarks,'mode'=>$oth->modeOfPayment,'dp'=>$oth->isDp]);
  }
  }
    @endphp

      <!-- Invoice Items Details -->
      <div id="invoice-items-details" class="pt-1 invoice-items-table">
        <div class="row">
          <div class="table-responsive col-sm-12">
            <table class="table zero-configuration">
              <thead>
              <tr>
                <th>Payment Date</th>
                <th>Amount</th>
                <th>Mode Of payment</th>
{{--                <th>Remarks</th>--}}
                <th>DP</th>
              </tr>
              </thead>
              <tbody>
              @foreach($totalTransactions as $transaction)
              @if(!$transaction['amount'] == 0)
                <tr>
                  <td>{{ \Carbon\Carbon::parse($transaction['date'])->format('d-M-Y') }}</td>
                  <td>{{ inr($transaction['amount']) }}</td>
                  <td>{{ $transaction['mode'] }}</td>
{{--                  <td>{{ $transaction['remarks'] }}</td>--}}
                  <td>{{ $transaction['dp'] == 1 ? 'Downpayment' : 'EMI' }}</td>
                </tr>
                @endif
              @endforeach
              </tbody>

              <tfoot>
              <tr>
                <th>Payment Date</th>
                <th>Amount</th>
                <th>Mode Of payment</th>
{{--                <th>Remarks</th>--}}
                <th>DP</th>
              </tr>
              </tfoot>
            </table>


          </div>
        </div>
      </div>
      <br>
      <br>
      <br>
      <hr>

      <div id="invoice-total-details" class="invoice-total-table">
        <div class="row">
          <div class="col-7 offset-5">
            <div class="table-responsive">
              <table class="table table-borderless">
                <tbody>
                <tr>
                  <th>FCLP COST</th>
                  <td>{{ inr($client->latestPackage->productCost) }}</td>
                </tr>
                <tr>
                  <th>Downpayment</th>
                  <td>{{ inr($client->downPayment) }}</td>
                </tr>
                <tr>
                  <th>Total Payment Done(incl. DP)</th>
                  <td>{{ inr($totalTransactions->pluck('amount')->sum()) }}</td>
                </tr>
                <tr>
                  <th>Pending Amount</th>
                  <td>{{ inr($package->productCost - $client->paidAmount) }}</td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Invoice Footer -->
      <div id="invoice-footer" class="text-right pt-3">
        <p>Forbcorp Pvt. Ltd.
{{--        <p class="bank-details mb-0">--}}
{{--          <span class="mr-4">BANK: <strong>FTSBUS33</strong></span>--}}
{{--          <span>IBAN: <strong>G882-1111-2222-3333</strong></span>--}}
{{--        </p>--}}
      </div>
      <!--/ Invoice Footer -->
    </div>
  </section>
@endsection
@section('page-script')
  <!-- Page js files -->
  <script src="{{ asset(mix('js/scripts/pages/invoice.js')) }}"></script>
@endsection

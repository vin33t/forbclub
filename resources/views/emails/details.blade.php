<!DOCTYPE html>
<html lang="en">
<!-- BEGIN HEAD -->
<head>
  <!-- Hotjar Tracking Code for app.forbclub.com -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="width=device-width, initial-scale=1" name="viewport" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>Forb Club Leisureship Details</title>

  <!-- google font -->
  <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700,900&display=swap" rel="stylesheet" media="all">
  <link href="https://fonts.googleapis.com/css?family=Cinzel:400,700&display=swap" rel="stylesheet" media="all">
  <!-- icons -->

  <link href="https://app.forbclub.com/theme/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" media="all"/>
  <!--bootstrap -->
  <link href="https://app.forbclub.com/theme/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all"/>

  <link href="https://app.forbclub.com/theme/assets/css/style.css" rel="stylesheet" type="text/css" media="all"/>
  <!-- favicon -->
  <link rel="shortcut icon" href="https://app.forbclub.com/theme/assets/img/favicon.ico" />


  <style>
    body{
      font-size:16px;
    }
    .welcome-letter {

    }
    .welcome-letter h1{
      font-family: 'Cinzel', serif;
      font-size:80px !important;
      margin-bottom:0;
      background: -webkit-linear-gradient(left, #8f6B29 20%, #ffda71, #DF9F28);
      background: linear-gradient(left, #8f6B29 20%, #ffda71, #DF9F28);
      -webkit-text-fill-color:transparent;
      -webkit-background-clip:text;
    }
    .inner-welcome-content-gradient {
      position: relative;
      border-image-source: -webkit-linear-gradient(left, #8f6B29, #ffda71, #DF9F28);
      border-image-source: linear-gradient(left, #8f6B29, #ffda71, #DF9F28);
      border-style: solid;
      border-width: 45px;
      border-image-slice: 1;
      /*background-image: -webkit-linear-gradient(left, #8f6B29, #ffda71, #DF9F28);
      background-image: linear-gradient(left, #8f6B29, #ffda71, #DF9F28);*/
      padding:0;
    }
    .inner-welcome-content {
      background-color: rgba(255,255,255,0.8);
      padding:3rem;
    }
    .details h4 {
      border-bottom: 1px solid #f1f1f1;
      margin: 0;
      padding: 0.7rem 0;
      font-size: 1rem !important;
    }
    .details h4 span {
      width: 40%;
      display: inline-block;
    }
  </style>
</head>
<!-- END HEAD -->


<body>


<section>
  <div class="container bg-white welcome-letter details">
    <div class="row justify-content-center">
      <div class="col-md-12 inner-welcome-content-gradient">
        <div class="inner-welcome-content">
          <div class="text-center">
            <img src="https://forbclub.com/images/logo.png" class="img-fluid mb-5" alt="forbclub" style="width:300px;"/>
          </div>

          <h3 class="font-weight-bold mb-3" style="color:#2c3c64;">Leisureship Details:</h3>
          <h4><span>Forb Club Leisureship ID</span>  FCLP{{$client->latestPackage->fclpId}}</h4>
          <h4><span>Member Name</span>  {{$client->name}}</h4>
          <h4><span>Product</span> {{$client->latestPackage->productName}}</h4>
          <h4><span>Product Tenure</span> {{ $client->latestPackage->productTenure }}</h4>
          <h4><span>Enrolment Date(ED)</span>  {{ Carbon\Carbon::parse($client->latestPackage->enrollmentDate)->format('d/m/Y') }}</h4>
{{--          @if($details->packages->first()->product_tenure == 10 OR $details->packages->first()->product_tenure == 15) @else <h4><span>Holiday Entitlement Month(HEM)</span> {{ Carbon\Carbon::parse($details->date_of_enrollment)->addMonths(7)->format('F Y\\') }}@endif</h4>--}}
          <h4><span>Product Cost</span>  INR. {{$client->latestPackage->productCost}}/-</h4>
{{--          <h4><span>Product Discount</span> {{$details->fclp_discount}}</h4>--}}
          <h4><span>Referred by</span>  NA</h4>
          <p class="font-weight-bold mt-3">*Full Payment - Discount is applicable on the payment collected at the time of sale.</p><br><hr>

          <h3 class="font-weight-bold mb-3" style="color:#2c3c64;">Payment Details:</h3>
          <h4><span>Down Payment</span> {{ $client->downPayment }} /-</h4>
          <h4><span>Number of EMI's</span> {{$client->latestPackage->noOfEmi}}</h4>
          <!--                <h4><span>EMI Amount</span> INR 3,000/-</h4>-->
          <h4><span>EMI Due Date</span> 5th of every month till {{ Carbon\Carbon::parse($client->latestPackage->enrollmentDate)->addMonths($client->latestPackage->noOfEmi)->format('F Y\\') }}</h4>
          <h4><span>Annual Service Charges (ASC)* Amount</span>  @if($client->latestPackage->asc != NULL) {{ $client->latestPackage->asc }} @else NA @endif</h4>
{{--          <h4><span>ASC Due</span>  NA</h4>--}}
          <p class="font-weight-bold mt-3">*Annual Service Charges may be revised from time by Forb and the Member undertakes to pay the ASC every year within the time stipulated. Tax charges indicated is as per the rates applicable as on date. This is subject to changes as per Govt. Notification. </p><br><hr>

          <h3 class="font-weight-bold mb-3" style="color:#2c3c64;">Special Offer(s)*</h3>
          <h4><span>LAC Complimentary Enrolment</span>
            <span>
                                @forelse($client->latestPackage->Benefits as $benefit)
                {{ $benefit->benefitName }} - {{ $benefit->benefitDescription  }}
                @if($client->latestPackage->Benefits->count() > 1) + @endif
              @empty
                {{ 'No Enrolment Benefits' }}
              @endforelse
                            </span>
          </h4>
          <h4><span>Complimentary Transfers</span>  NA</h4>
          <h4><span>Complimentary Overseas Travel Insurance</span>  NA</h4>
          <h4><span>Multi-Currency Card</span>  NA</h4>
          <h4><span>Free Visa Processing</span>  @if($client->latestPackage->productName == 'Classic FCV' or $client->latestPackage->productName == 'India FCV') N/A @else  {{ $client->latestPackage->productTenure }} Year(s) @endif </h4>
          <h4><span>Fully Paid Holiday</span>  @if($client->latestPackage->productName == 'Classic FCV' or $client->latestPackage->productName == 'India FCV') N/A @else 1{5N/6D 02 Adults} @endif</h4>
          @if($client->latestPackage->productName == 'Classic' AND $client->latestPackage->productTenure == 10)<h4> <span>Stay Only Holiday</span>  9  </h4>@endif
          @if($client->latestPackage->productName == 'Classic FCV' or $client->latestPackage->productName == 'India FCV')
            <h4><span>Leisureship</span>  @if(!$client->latestPackage->productTenure) @if($client->latestPackage->productName == 'Classic FCV' or $client->latestPackage->productName == 'India FCV') 5 Years(Only Stay) @else 4 Year(s) @endif   @else {{ $client->latestPackage->productTenure }} Years @endif </h4>
          @endif
          <h4><span>Extra</span>  NA</h4>
          <p class="font-weight-bold mt-3">*Terms and conditions applicable </p>
          <p class="text-danger">The above offers can be availed upon fulfilment of your payment eligibility. The Special Offers are subject to availability within the offer validity period.</p><br><hr>

          <address class="text-center mt-5 mb-2">
            <span class="h4 d-block" style="color:#2c3c64;"><b>Forbcorp Private Limited</b></span>
            <b>Corporate & Sales Office:</b> SCO 116, Sector 47C, Chandigarh (U.T), India – 160047
          </address>
        </div>
      </div>
    </div>
  </div>
</section>

</body>
</html>

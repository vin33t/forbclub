<div class="col-lg-3 col-12">
  <div class="card">
    <div class="card-header">
      <h4>Bookings <a href="{{ route('create.client.booking',['slug'=>$client->slug]) }}">@if($user->employee)<button class="btn btn-primary">+</button></a>@endif</h4>
    </div>
    <div class="card-body">
      @foreach($client->Bookings as $booking)
      <div class="d-flex justify-content-start align-items-center mb-1">
        <div class="avatar mr-50">
          <img src="{{ avatar($booking->holidayType) }}" alt="avtar img holder" height="35"
               width="35">
        </div>
        <div class="user-page-info">
          <h6 class="mb-0">{{ $booking->holidayType }} <strong>({{ $booking->bookingRequestNumber }})</strong></h6>
        </div>
{{--        <button type="button" class="btn btn-primary btn-icon ml-auto"  data-toggle="modal" data-target="#pacakgeBenefit{{$booking->id}}"><i class="feather icon-eye"></i>--}}
        </button>

      </div>
        @endforeach

    </div>
  </div>
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h4>Package Benefits</h4>
{{--      <i class="feather icon-more-horizontal cursor-pointer"></i>--}}
    </div>
    <div class="card-body">
      @forelse($client->Packages->first()->Benefits->unique('benefitName') as $benefit)
      <div class="d-flex justify-content-start align-items-center mb-1">
        <div class="avatar mr-50">
          <img src="{{ avatar($benefit->benefitName) }}" alt="avtar img holder" height="35"
               width="35">
        </div>
        <div class="user-page-info">
          <h6 class="mb-0">{{ $benefit->benefitName }}</h6>
          <span class="font-small-2">{{ $client->Packages->first()->Benefits->where('benefitName',$benefit->benefitName)->where('benefitAvailedOn',null)->count() }} Available</span>
        </div>
        <button type="button" class="btn btn-primary btn-icon ml-auto"  data-toggle="modal" data-target="#pacakgeBenefit{{$benefit->id}}"><i class="feather icon-eye"></i>
        </button>
        <div class="modal fade" id="pacakgeBenefit{{$benefit->id}}" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">{{ $benefit->benefitName }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                @php
                $bnfts = $client->Packages->first()->Benefits->where('benefitName',$benefit->benefitName);
                @endphp
                <ul>
                @foreach($bnfts as $bnft)
                  <li>{{ $bnft->benefitName }} - {{ $bnft->benefitDescription }}</li>
                @endforeach
                </ul>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
{{--                <button type="button" class="btn btn-primary">Save changes</button>--}}
              </div>
            </div>
          </div>
        </div>

      </div>
      @empty
        No Package Benefits Available
      @endforelse
        @if($user->employee)
        <button type="button" class="btn btn-primary w-100 mt-1" onclick="$('#addPackageBenefitModal').modal()">Add</button>
@endif
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <h4 class="card-title">Payments</h4>
    </div>
    <div class="card-content">
      <div class="card-body">
        <ul class="nav nav-tabs" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#chart" aria-controls="chart" role="tab"
               aria-selected="true">Chart</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="summary-tab" data-toggle="tab" href="#summary" aria-controls="summary"
               role="tab" aria-selected="false">Summary</a>
          </li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="chart" aria-labelledby="chart-tab" role="tabpanel">
            <strong>
            Down Payment: {{ $client->downPayment }} <br>
            Total Paid Amount(incl. DP): {{ $client->paidAmount }} <br>
            Add On Amount: {{ $client->addOnAmount }} <br>
            Pending Amount: {{ $client->latestPackage->productCost - $client->paidAmount }}
            </strong>
            @if(count($client->transactionSummaryChart))
            <div id="transaction-summary-pie-chart" class="height-400"></div>
            @else
              No Transactions Available
            @endif
          </div>
          <div class="tab-pane" id="summary" aria-labelledby="summary-tab" role="tabpanel">
            <div class="card-body">
              Down Payment: {{ $client->downPayment }}

            @if(count($client->transactionSummaryChart))
                @foreach($client->transactionSummary as $transaction)
                  <h6>
                    {!! $transaction !!}
                  </h6>
                @endforeach
              @else
                No Transactions Available
              @endif
            </div>
          </div>

        </div>
      </div>
    </div>


  </div>
</div>
@include('client.components.addPackageBenefit',['client'=>$client])




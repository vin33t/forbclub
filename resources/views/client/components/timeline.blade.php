<div class="col-lg-6 col-12">
  <div class="row">


    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
         <h4>Package Details</h4>
          <hr>
          <div class="table-responsive">
            <table class="table">
              <tbody>
              <tr>
                <th scope="row">Product Name</th>
                <th scope="row">{{ $client->latestPackage->productName }}</th>
              </tr>
                <tr>
                  <th scope="row">Product Cost</th>
                  <th scope="row">{{ $client->latestPackage->productCost }}</th>
                </tr>
                <tr>
                  <th scope="row">Number of EMI's</th>
                  <th scope="row">{{ $client->latestPackage->noOfEmi }}</th>
                </tr>
                <tr>
                  <th scope="row">Total EMI Amount</th>
                  <th scope="row">Total EMI Amount</th>
                </tr>
                <tr>
                  <th scope="row">EMI Amount</th>
                  <th scope="row">EMI Amount</th>
                </tr>
                <tr>
                  <th scope="row">EMI Due Date</th>
                </tr>
                <tr>
                  <th scope="row">Annual Service Charges(ASC) *Amount</th>
                </tr>
                <tr>
                  <th scope="row">ASC Due</th>
                </tr>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
         <h4>Payment Details</h4>
          <hr>
          <div class="table-responsive">
            <table class="table">
              <tbody>
              <tr>
                <th scope="row">Down Payment</th>
                <th scope="row">
                  @php
                    $cardPayments = $client->CardPayments->where('isDp',1)->pluck('amount')->sum();
                    $cashPayments = $client->CashPayments->where('isDp',1)->pluck('amount')->sum();
                    $chequePayments = $client->ChequePayments->where('isDp',1)->pluck('amount')->sum();
                    $otherPayments = $client->OtherPayments->where('isDp',1)->pluck('amount')->sum();
                  @endphp
                  {{ $cardPayments + $cashPayments + $chequePayments + $otherPayments }}
                </th>
              </tr>
                <tr>
                  <th scope="row">EMI Mode of Payment</th>
                  <th scope="row">
                    @if($client->AxisPayments->count()) AXIS NACH
                    @elseif($client->YesPayments->count()) Yes NACH
                      @else
                      N/A
                    @endif
                  </th>
                </tr>
                <tr>
                  <th scope="row">Number of EMI's</th>
                </tr>
                <tr>
                  <th scope="row">Total EMI Amount</th>
                </tr>
                <tr>
                  <th scope="row">EMI Amount</th>
                </tr>
                <tr>
                  <th scope="row">EMI Due Date</th>
                </tr>
                <tr>
                  <th scope="row">Annual Service Charges(ASC) *Amount</th>
                </tr>
                <tr>
                  <th scope="row">ASC Due</th>
                </tr>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>

    {{--    @foreach($client->TimelineActivity->sortByDesc('created_at') as $activity)--}}
{{--      <div class="col-md-12">--}}
{{--        <div class="card">--}}
{{--          <div class="card-body">--}}
{{--            <div class="d-flex justify-content-start align-items-center mb-1">--}}
{{--              <div class="avatar mr-1">--}}
{{--                <img src="{{ avatar($activity->User->name) }}" alt="avtar img holder" height="45"--}}
{{--                     width="45">--}}
{{--              </div>--}}
{{--              <div class="user-page-info">--}}
{{--                <h6 class="mb-0">{{ $activity->User->name }}</h6>--}}
{{--                <span class="font-small-2">{{readableDate($activity->created_at) }}</span>--}}
{{--              </div>--}}
{{--            </div>--}}
{{--            <p>{{ $activity->title }}</p>--}}
{{--            {!! $activity->body !!}--}}
{{--            <div class="d-flex justify-content-start align-items-center mb-1">--}}

{{--              <p class="ml-auto d-flex align-items-center">--}}
{{--                <i class="feather icon-message-square font-medium-2 mr-50"></i>{{ $activity->comments->count() }}--}}
{{--              </p>--}}
{{--            </div>--}}
{{--            @forelse($activity->comments as $comment)--}}
{{--              <div class="d-flex justify-content-start align-items-center mb-1">--}}
{{--                <div class="avatar mr-50">--}}
{{--                  <img src="{{ avatar($comment->User->name) }}" alt="Avatar" height="30" width="30">--}}
{{--                </div>--}}
{{--                <div class="user-page-info">--}}
{{--                  <h6 class="mb-0">{{ $comment->User->name }} @ <span class="font-small-2">{{ readableDate($comment->created_at) }}</span></h6>--}}
{{--                  <span class="font-small-4">{{ $comment->body }}</span>--}}
{{--                  <span class="font-small-1"></span>--}}
{{--                </div>--}}
{{--              </div>--}}
{{--            @empty--}}
{{--              No Comments--}}
{{--            @endforelse--}}
{{--            <form action="{{ route('create.client.timelineComment',['activityId'=>$activity->id]) }}" method="POST">--}}
{{--              @csrf--}}
{{--              <fieldset class="form-label-group mb-50">--}}
{{--                <textarea class="form-control" id="label-textarea3" rows="3" placeholder="Add Comment" name="activityComment"></textarea>--}}
{{--                <label for="label-textarea3">Add Comment</label>--}}
{{--              </fieldset>--}}
{{--              <button type="submit" class="btn btn-sm btn-primary">Save Comment</button>--}}
{{--            </form>--}}
{{--          </div>--}}
{{--        </div>--}}
{{--      </div>--}}
{{--    @endforeach--}}

  </div>
</div>

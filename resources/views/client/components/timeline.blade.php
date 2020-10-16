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
                <th scope="row">FCLP ID</th>
                <th scope="row">{{ $client->latestPackage->fclpId }}</th>
              </tr>
              <tr>
                <th scope="row">MAF No</th>
                <th scope="row">{{ $client->latestPackage->mafNo }}</th>
              </tr>
                <tr>
                  <th scope="row">Product Cost</th>
                  <th scope="row">{{ $client->latestPackage->productCost }}</th>
                </tr>
                <tr>
                  <th scope="row">Number of EMI's</th>
                  <th scope="row">{{ $client->latestPackage->noOfEmi }}</th>
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
            @if($client->disableNach->count())
          <div class="alert-danger">
            Nach Disabled
              @if($client->disableNach->pluck('permanent')->contains(1))
                Permanently ({{ $client->disableNach->where('permanent',1)->first()->remarks }})
                @else
              for
              @foreach($client->disableNach as $dn)
                {{ $dn->month }} {{ $dn->year }}({{ $dn->remarks }}),
              @endforeach
                @endif
          </div>
            @endif
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
                    @if($client->latestPackage->modeOfPayment != '')
                      {{ $client->latestPackage->modeOfPayment }}
                    @elseif($client->AxisPayments->count()) AXIS NACH
                    @elseif($client->YesPayments->count()) Yes NACH
                      @else
                      N/A
                    @endif
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#updateModeOfPayment" >Update</button>
                  </th>
                </tr>
                <tr>
                  <th scope="row">Total EMI Amount</th>
                  <th scope="row">{{ $client->latestPackage->productCost - $cardPayments + $cashPayments + $chequePayments + $otherPayments }}</th>
                </tr>
                <tr>
                  <th scope="row">EMI Amount</th>
                  <th scope="row">{{ round(($client->latestPackage->productCost - $cardPayments + $cashPayments + $chequePayments + $otherPayments) / $client->latestPackage->noOfEmi )}}</th>
                </tr>
                <tr>
                  <th scope="row">EMI Due Date</th>
                  <th scope="row">5<sup>th</sup> of Every Month</th>
                </tr>
                <tr>
                  <th scope="row">Annual Service Charges(ASC) *Amount</th>
                  <th scope="row">
                    @if($client->latestPackage->asc)
                      {{ $client->latestPackage->asc }}
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addAsc" >Update</button>
                      @else
                    N/A
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addAsc" >Add</button>
                      @endif
                  </th>
                </tr>

              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
    <div class="modal fade" id="updateModeOfPayment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Update Mode Of Payment</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="{{ route('update.modeOfPayment') }}" method="post">
            @csrf
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <input type="hidden" name="client" value="{{ $client->id }}">
                <label for="modeOfPayment">Mode Of Payment</label>
                <select name="modeOfPayment" id="modeOfPayment" class="form-control" required>
                  <option value="">--Select Mode of Payment--</option>
                  <option value="Cash">Cash</option>
                  <option value="Card">Card</option>
                  <option value="Online">Online</option>
                  <option value="Axis NACH">Axis NACH</option>
                  <option value="Yes NACH">Yes NACH</option>
                  <option value="Cheque">Cheque</option>
                  <option value="No Formality">No Formality</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="Submit" class="btn btn-primary">Update</button>
          </div>
          </form>
        </div>
      </div>
    </div>
    <div class="modal fade" id="addAsc" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Add Annual Subscription Charges</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="{{ route('add.asc') }}" method="post">
            @csrf
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <input type="hidden" name="client" value="{{ $client->id }}">
                <label for="asc">Annual Subscription Charges</label>
                <input type="number" name="asc" id="asc" class="form-control" required>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="Submit" class="btn btn-primary">Add</button>
          </div>
          </form>
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

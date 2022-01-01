<div class="col-lg-6 col-12">
  <div class="row">
    @if(request()->status)
      <div class="col-md-12">
        <div class="alert alert-success" role="alert">
          Query Sent
        </div>
      </div>
    @endif
    @if(!$user->employee)
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h4>Queries</h4>
            <br>
            <button class="btn btn-primary" data-toggle="modal" data-target="#bookingQuery">Booking Query</button>
            <div class="modal fade" id="bookingQuery" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">New Booking Query</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <form action="{{ route('bookingQuery') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                      <input type="hidden" name="clientId" value="{{ $client->id }}" required>
                      <div class="row">
                        <div class="col-md-6">
                          <label for="destination">Destination</label>
                          <input type="text" name="destination" id="destination" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                          <label for="travelDate">Travel Date</label>
                          <input type="date" name="travelDate" id="travelDate" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                          <label for="adults">No Of Adults</label>
                          <input type="number" name="adults" id="adults" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                          <label for="kids">No Of Kids</label>
                          <input type="number" name="kids" id="kids" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                          <label for="nights">No Of Nights</label>
                          <input type="number" name="nights" id="nights" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                          <label for="rooms">No Of Rooms</label>
                          <input type="number" name="rooms" id="rooms" class="form-control" required>
                        </div>

                        <div class="col-md-12">
                          <label for="queryRemarks">Remarks</label>
                          <textarea name="queryRemarks" id="queryRemarks" cols="30" rows="10"
                                    class="form-control"></textarea>
                          {{--                          <input type="text" name="otherQuery" id="otherQuery" required>--}}
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <button class="btn btn-success" data-toggle="modal" data-target="#otherQuery">Other Query</button>
            <div class="modal fade" id="otherQuery" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">New Query</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <form action="{{ route('otherQuery') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                      <input type="hidden" name="clientId" value="{{ $client->id }}" required>
                      <div class="row">
                        <div class="col-md-12">
                          <label for="otherQuery">Other Query</label>
                          <textarea name="otherQuery" id="otherQuery" cols="30" rows="10"
                                    class="form-control"></textarea>
                          {{--                          <input type="text" name="otherQuery" id="otherQuery" required>--}}
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <h4>Package Details </h4>
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
                <th scope="row">
                  @if($client->emiRegularPlan){{ $client->emiRegularPlan }} @else {{ $client->latestPackage->noOfEmi }} @endif
                </th>
              </tr>
              <tr>
                <th scope="row">Fully Paid Holiday</th>
                <th scope="row">
                  @if($client->latestPackage->productType == 'Classic FCV' or $client->latestPackage->productType == 'India FCV' or $client->latestPackage->productType == 'fcv' )
                    N/A @else 1{5N/6D 02 Adults} @endif</th>
              </tr>
              <tr>
                <th scope="row">Stay only Holiday</th>
                <th scope="row">
                  @if($client->latestPackage->productType == 'Classic FCV' or $client->latestPackage->productType == 'India FCV'  or $client->latestPackage->productType == 'fcv') {{ $client->latestPackage->productTenure }}
                  Years @else {{$client->latestPackage->productTenure - 1}} Years @endif
                </th>
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
                    $cardPayments = $client->CardPayments->pluck('amount')->sum();
                    $cashPayments = $client->CashPayments->pluck('amount')->sum();
                    $chequePayments = $client->ChequePayments->pluck('amount')->sum();
                    $otherPayments = $client->OtherPayments->pluck('amount')->sum();
                  @endphp
                  {{ $client->downPayment }}
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
                  <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#updateModeOfPayment">Update
                  </button>
                </th>
              </tr>
              <tr>
                <th scope="row">Total EMI Amount</th>
                <th
                  scope="row">{{ $client->latestPackage->productCost - ($cardPayments + $cashPayments + $chequePayments + $otherPayments) }}</th>
              </tr>
              <tr>
                <th scope="row">EMI Amount</th>
                {{--                  @if($client->latestPackage->noOfEmi)--}}
                {{--                  <th scope="row">{{ round(($client->latestPackage->productCost - $cardPayments + $cashPayments + $chequePayments + $otherPayments) / $client->latestPackage->noOfEmi )}}</th>--}}
                {{--                  @else--}}
                {{--                    <th scope="row">{{ $client->latestPackage->emiAmount }}</th>--}}
                {{--                  @endif  --}}

                @if($client->latestPackage->emiAmount == 0)
                  @if($client->latestPackage->noOfEmi)
                    <th
                      scope="row">{{ round(($client->latestPackage->productCost - $cardPayments + $cashPayments + $chequePayments + $otherPayments) / $client->latestPackage->noOfEmi )}}</th>
                  @endif
                @else
                  <th scope="row">{{ $client->latestPackage->emiAmount }}</th>
                @endif
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
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addAsc">Update</button>
                  @else
                    N/A
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addAsc">Add</button>
                  @endif
                </th>
              </tr>

              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>

    @if($user->employee)

      {{--    Follow Ups--}}
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h4>Follow Ups
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addFollowUp">Add New</button>
              </h4>
            </div>
            @if($client->followUp->count())
              <div class="card-body">
                <ul>
                  @foreach($client->followUp as $followUp)
                    <li><strong>{{$followUp->subject}} | Added On: {{ \Carbon\Carbon::parse($followUp->follow_up_on)->format('d F, Y') }} |</strong>
                      @if(Auth::user()->name == $followUp->type)
                        @if(\Carbon\Carbon::parse($followUp->created_at)->format('Y-m-d') == \Carbon\Carbon::now()->format('Y-m-d'))
                          <button class="btn btn-primary btn-sm" data-toggle="modal"
                                  data-target="#editFollowUp{{ $followUp->id }}">Edit
                          </button>
                          <form action="{{ route('delete.followUp',['id'=>$followUp->id]) }}" method="POST">
                            @csrf
                            <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                          </form>
                        @endif
                      @endif

                      <p>{!! $followUp->details !!}</p>
                      <strong>Added By: {{ $followUp->type }}</strong></li>
                    <div class="modal fade" id="editFollowUp{{ $followUp->id }}" tabindex="-1" role="dialog"
                         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Edit Follow
                              Up {{ $followUp->subject }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <form action="{{ route('update.followUp',['id'=>$followUp->id]) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                              <div class="row">
                                <div class="col-md-12">
                                  <input type="hidden" name="id" value="{{ $client->id }}">
                                  <label for="followUpDate">Follow Up Date</label>
                                  <input type="date" name="followUpDate" class="form-control"
                                         placeholder="Follow Up Date" value="{{ $followUp->follow_up_on }}" required>
                                </div>
                                <div class="col-md-12">
                                  {{--                <label for="followUpType">Added By</label>--}}
                                  <input type="hidden" name="followUpType" class="form-control"
                                         placeholder="Follow Up Type" required value="{{ Auth::user()->name }}">
                                </div>
                                <div class="col-md-12">
                                  <label for="followUpSubject">Follow Up Subject</label>
                                  <input type="text" name="followUpSubject" class="form-control"
                                         placeholder="Follow Up Subject" required value={{ $followUp->subject }}>
                                </div>
                                <div class="col-md-12">
                                  <label for="followUpRemarks">Follow Up Remarks</label>
                                  <textarea name="followUpRemarks" id="" cols="30" rows="10" class="form-control"
                                            required>{!! $followUp->details !!}</textarea>
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

                  @endforeach
                </ul>
              </div>
            @else
              <div class="card-body">
                No Notes Yet
              </div>
            @endif
          </div>
        </div>
      </div>
      <!-- Modal -->
      <div class="modal fade" id="addFollowUp" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
           aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Add Follow UP</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form action="{{ route('add.followUp') }}" method="POST">
              @csrf
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-12">
                    <input type="hidden" name="id" value="{{ $client->id }}">
                    <label for="followUpDate">Follow Up Date</label>
                    <input type="date" name="followUpDate" class="form-control" placeholder="Follow Up Date"
                           value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                  </div>
                  <div class="col-md-12">
                    <label for="followUpReminder">Follow Up Reminder(Optional)</label>
                    <input type="date" name="followUpReminder" class="form-control" placeholder="Follow Up Reminder">
                  </div>
                  <div class="col-md-12">
                    {{--                <label for="followUpType">Added By</label>--}}
                    <input type="hidden" name="followUpType" class="form-control" placeholder="Follow Up Type" required
                           value="{{ Auth::user()->name }}">
                  </div>
                  <div class="col-md-12">
                    <label for="followUpSubject">Follow Up Subject</label>
                    <input type="text" name="followUpSubject" class="form-control" placeholder="Follow Up Subject"
                           required>
                  </div>
                  <div class="col-md-12">
                    <label for="followUpRemarks">Follow Up Remarks</label>
                    <textarea name="followUpRemarks" id="" cols="30" rows="10" class="form-control" required></textarea>
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
      {{--end followUp--}}
    @endif

      @if($user->employee)

        {{--    Documents --}}
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h4>Documents
                  <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addDocument">Add New</button>
                </h4>
              </div>

                <div class="card-body">
                  No Documents Yet
                </div>

            </div>
          </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="addDocument" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
             aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form action="{{ route('add.document') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                  <div class="row">
                    <div class="col-md-12">
                      <input type="hidden" name="clientId" value="{{ $client->id }}">
                      <label for="documentDescription">Document Description</label>
                      <input type="text" name="documentDescription" class="form-control" placeholder="Document/File Description"
                             required>
                    </div>
                    <div class="col-md-12">
                      <label for="document">Document</label>
                      <input type="file" name="document" class="form-control" placeholder="Document" required>
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
        {{--end followUp--}}
      @endif


    @if($user->employee)

      @if($client->Pdc->count())
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <h4>PDC</h4>
              <hr>

              <div class="table-responsive">
                <table class="table">
                  <thead>
                  <tr>
                    <th scope="row">Cheque Number</th>
                    <th scope="row">Amount</th>
                    <th scope="row">MICR Number</th>
                    <th scope="row">Branch Name</th>
                    <th scope="row">Date Of Execution</th>
                    <th scope="row">Status</th>
                    <th scope="row">Remarks</th>
                  </tr>

                  </thead>
                  <tbody>
                  @foreach($client->Pdc as $pdc)
                    <tr>
                      <td>{{ $pdc->cheque_no }}</td>
                      <td>{{ $pdc->amount }}</td>
                      <td>{{ $pdc->micr_number }}</td>
                      <td>{{ $pdc->branch_name }}</td>
                      <td>{{ $pdc->date_of_execution }}</td>
                      <td>
                        @if($pdc->status == 'unused')
                          <button class="btn btn-primary btn-sm" data-toggle="modal"
                                  data-target="#updatePDC{{$pdc->id}}">{{ $pdc->status }}</button>
                        @else
                          {{ $pdc->status }}
                        @endif
                      </td>
                      <td>{{ $pdc->remarks }}</td>

                    </tr>
                    <div class="modal fade" id="updatePDC{{$pdc->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Update PDC Status</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <form action="{{ route('update.pdc.status',['id'=>$pdc->id]) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                              <div class="row">
                                <div class="col-md-12">
                                  <label for="">Status</label>
                                  <select name="status" class="form-control">
                                    <option value="">--SELECT--</option>
                                    <option value="unused">Unused</option>
                                    <option value="CLEARED">Cleared</option>
                                    <option value="BOUNCED">Bounced</option>
                                  </select>
                                </div>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                              <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  @endforeach
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>
      @endif


      <div class="modal fade" id="updateModeOfPayment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
           aria-hidden="true">
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
      <div class="modal fade" id="addAsc" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
           aria-hidden="true">
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

    @endif

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

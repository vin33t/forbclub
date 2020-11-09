@extends('layouts/contentLayoutMaster')

@section('title', 'Refund Requests')

@section('vendor-style')
  {{-- vendor css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
@endsection

@section('content')
  <ul class="nav nav-tabs tabs-design" id="myTab" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#refund_requests" role="tab" aria-controls="profile">Refund Requests</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="profile-tab" data-toggle="tab" href="#accepted" role="tab" aria-controls="profile">Accepted By Mrd</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="profile-tab" data-toggle="tab" href="#denied" role="tab" aria-controls="profile">Denied By Mrd</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="profile-tab" data-toggle="tab" href="#approved" role="tab" aria-controls="profile">Approved By Manager</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="profile-tab" data-toggle="tab" href="#rejected" role="tab" aria-controls="profile">Rejected By Manager</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="profile-tab" data-toggle="tab" href="#accounts-approved" role="tab" aria-controls="profile">Scheduled By Accounts</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="profile-tab" data-toggle="tab" href="#transactions" role="tab" aria-controls="profile">Transactions</a>
    </li>
  </ul>
  <div class="tab-content" id="myTabContent">

    <div class="tab-pane fade show active" id="refund_requests" role="tabpanel" aria-labelledby="home-tab">

      <div class="row">
        <div class="col-sm-12">
          <div class="card">
            <div class="card-header">
              <h3 class="text-center">New Refund Request</h3>
            </div>
            <div class="card-body">
              <table class="table table-bordered">
                <thead>
                <tr>
                  <th>Client Name</th>
                  <th>Added By</th>
                  <th>Amount</th>
                  <th>Refund Date</th>
                  <th>Reason</th>
                  <th>Accept/Deny</th>
                </tr>
                </thead>
                <tbody>
                @forelse(\App\RefundRequests::where('accepted_denied',null)->orWhere('accepted_denied',0)->get() as $rr)
                  <tr>
                    <td><a href="{{ route('view.client',['slug'=>$rr->client->slug]) }}">{{ $rr->client->name  }}</a></td>
                    <td>{{ App\User::find($rr->added_by)->name }}</td>
                    <td>{{ $rr->amount}}</td>
                    <td>{{ $rr->refund_date }}</td>
                    <td>{{ $rr->reason }}</td>
                    <td>
                      <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#accept"
                              onclick="$('#acceptHtml').val({{$rr->id}});">Accept</button>
                      <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deny"
                              onclick="$('#denyHtml').val({{$rr->id}});">Deny</button>
                      {{-- <a href="{{route('refund.request.accept.deny',['service',1,$rr->id])}}" class="btn btn-sm btn-success">Accept</a>
                      <a href="{{route('refund.request.accept.deny',['service',2,$rr->id])}}" class="btn btn-sm btn-danger">Deny</a> --}}
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" class="text-center">No Records!</td>
                  </tr>
                @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="accept" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" >
        <div class="modal-dialog modal-dialog-centered" role="document" >
          <div class="modal-content" >
            <div class="modal-header bg-light" >
              <h5 class="modal-title" id="exampleModalLongTitle" ><strong>Accept Refund Request</strong></h5>
            </div>
            <form action="{{ route('accept.refund.request') }}" method="post" >
              @csrf
              <div class="modal-body" >
                <input type="hidden" name="refund_request"  id="acceptHtml">
                <label for="accepted_denied_remarks" class="pull-left">Remarks</label>
                <textarea name="accepted_denied_remarks" id="accepted_denied_remarks" class="form-control" required></textarea>
              </div>
              <div class="modal-footer bg-light" >
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <input type="submit" id="sub" class="btn btn-info" value="Accept"/>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="modal fade" id="deny" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" >
        <div class="modal-dialog modal-dialog-centered" role="document" >
          <div class="modal-content" >
            <div class="modal-header bg-light" >
              <h5 class="modal-title" id="exampleModalLongTitle" ><strong>Deny Refund Request</strong></h5>
            </div>
            <form action="{{ route('deny.refund.request') }}" method="post" >
              @csrf
              <div class="modal-body" >
                <input type="hidden" name="refund_request"  id="denyHtml">
                <div class="row">
                  <div class="col-md-12">
                    <input type="checkbox" name="change_client_status" onclick="if(this.checked == true){ $('#toggle').show() }else{ $('#toggle').hide() }">Change Client Status <br>
                    <div id="toggle" style="display:none;">
                      &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="status" value="cancel" id="">Cancel
                      <input type="radio" name="status" value="forfiet" id="">Forfiet <br>
                    </div>
                    <label for="accepted_denied_remarks" class="pull-left">Remarks</label>
                    <textarea name="accepted_denied_remarks" id="accepted_denied_remarks" class="form-control" required></textarea>
                  </div>
                </div>
              </div>
              <div class="modal-footer bg-light" >
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <input type="submit" id="sub" class="btn btn-info" value="Deny"/>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>

    <div class="tab-pane fade show" id="accepted" role="tabpanel" aria-labelledby="home-tab">
      <div class="row">
        <div class="col-sm-12">
          <div class="card">
            <div class="card-header">
              <h3 class="text-center">Accepted Refund Request</h3>
            </div>
            <div class="card-body">
              <table class="table table-bordered">
                <thead>
                <tr>
                  <th>Client Name</th>
                  <th>Added By</th>
                  <th>Amount</th>
                  <th>Refund Date</th>
                  <th>Reason</th>
                  <th>Accepted By</th>
                  <th>Accepted Remarks</th>
                  <th>Accepted On</th>
                    <th>Approve/Reject</th>
                </tr>
                </thead>
                <tbody>
                @forelse(\App\RefundRequests::where('accepted_denied',1)->get() as $rr)
                  <tr>
                    <td><a href="{{ route('view.client',['slug'=>$rr->client->slug]) }}">{{ $rr->client->name  }}</a></td>
                    <td>{{ App\User::find($rr->added_by)->name }}</td>
                    <td>{{ $rr->amount}}</td>
                    <td>{{ $rr->refund_date }}</td>
                    <td>{{ $rr->reason }}</td>
                    <td>{{ App\User::find($rr->accepted_denied_by)->name }}</td>
                    <td>{{ $rr->accepted_denied_remarks }}</td>
                    <td>{{ $rr->accepted_denied_datetime }}</td>
                      <td>
                        @if(!$rr->approved_rejected)
                          <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#approve"
                                  onclick="$('#approveHtml').val({{$rr->id}});">Approve</button>
                          <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#reject"
                                  onclick="$('#rejectHtml').val({{$rr->id}});">Reject</button>
                        @else
                          @if($rr->approved_rejected == 1)
                            {{__('Approved')}}
                          @elseif($rr->approved_rejected == 2)
                            {{__('Rejected')}}
                          @endif
                        @endif
                      </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="9" class="text-center">No Records!</td>
                  </tr>
                @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="approve" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" >
        <div class="modal-dialog modal-dialog-centered" role="document" >
          <div class="modal-content" >
            <div class="modal-header bg-light" >
              <h5 class="modal-title" id="exampleModalLongTitle" ><strong>Approve Refund Request</strong></h5>
            </div>
            <form action="{{ route('approve.refund.request') }}" method="post" >
              @csrf
              <div class="modal-body" >
                <input type="hidden" name="refund_request"  id="approveHtml">
                <label for="amount" class="pull-left">Amount Approved</label>
                <input type="text" name="amount" class="form-control" required>
                <label for="approved_rejected_remarks" class="pull-left">Remarks</label>
                <textarea name="approved_rejected_remarks" id="approved_rejected_remarks" class="form-control" required></textarea>
              </div>
              <div class="modal-footer bg-light" >
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <input type="submit" id="sub" class="btn btn-info" value="Approve"/>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="modal fade" id="reject" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" >
        <div class="modal-dialog modal-dialog-centered" role="document" >
          <div class="modal-content" >
            <div class="modal-header bg-light" >
              <h5 class="modal-title" id="exampleModalLongTitle" ><strong>Reject Refund Request</strong></h5>
            </div>
            <form action="{{ route('reject.refund.request') }}" method="post" >
              @csrf
              <div class="modal-body" >
                <input type="hidden" name="refund_request"  id="rejectHtml">
                <div class="row">
                  <div class="col-md-12">
                    <input type="checkbox" name="change_client_status" onclick="if(this.checked == true){ $('#togglee').show() }else{ $('#togglee').hide() }">Change Client Status <br>
                    <div id="togglee" style="display:none;">
                      &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="status" value="cancel" id="">Cancel
                      <input type="radio" name="status" value="forfiet" id="">Forfiet <br>
                    </div>
                    <label for="approved_rejected_remarks" class="pull-left">Remarks</label>
                    <textarea name="approved_rejected_remarks" id="approved_rejected_remarks" class="form-control" required></textarea>
                  </div>
                </div>
              </div>
              <div class="modal-footer bg-light" >
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <input type="submit" id="sub" class="btn btn-info" value="Deny"/>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="tab-pane fade show" id="denied" role="tabpanel" aria-labelledby="home-tab">
      <div class="row">
        <div class="col-sm-12">
          <div class="card">
            <div class="card-header">
              <h3 class="text-center">Denied Refund Request</h3>
            </div>
            <div class="card-body">
              <table class="table table-bordered">
                <thead>
                <tr>
                  <th>Client Name</th>
                  <th>Added By</th>
                  <th>Amount</th>
                  <th>Refund Date</th>
                  <th>Reason</th>
                  <th>Denied By</th>
                  <th>Denied Remarks</th>
                  <th>Denied On</th>
                  <th>Client Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse(\App\RefundRequests::where('accepted_denied',2)->get() as $rr)
                  <tr>
                    <td><a href="{{ route('view.client',['slug'=>$rr->client->slug]) }}">{{ $rr->client->name  }}</a></td>
                    <td>{{ App\User::find($rr->added_by)->name }}</td>
                    <td>{{ $rr->amount}}</td>
                    <td>{{ $rr->refund_date }}</td>
                    <td>{{ $rr->reason }}</td>
                    <td>{{ App\User::find($rr->accepted_denied_by)->name }}</td>
                    <td>{{ $rr->accepted_denied_remarks }}</td>
                    <td>{{ $rr->accepted_denied_datetime }}</td>
                    <td>
                      @if($rr->accepted_denied_client_status_changed != null)
                        {{$rr->accepted_denied_client_status_changed}}
                      @else
                        {{__('No Change')}}
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="9" class="text-center">No Records!</td>
                  </tr>
                @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="tab-pane fade show" id="approved" role="tabpanel" aria-labelledby="home-tab">
      <div class="row">
        <div class="col-sm-12">
          <div class="card">
            <div class="card-header">
              <h3 class="text-center">Approved Refund Request</h3>
            </div>
            <div class="card-body">
              <table class="table table-bordered">
                <thead>
                <tr>
                  <th>Client Name</th>
                  <th>Added By</th>
                  <th>Amount</th>
                  <th>Amount Approved By Managers</th>
                  <th>Refund Date</th>
                  <th>Reason</th>
                  <th>Approved By</th>
                  <th>Approved Remarks</th>
                  <th>Approved On</th>
                  <th>Approval By Accounts</th>
                </tr>
                </thead>
                <tbody>
                @forelse(\App\RefundRequests::where('approved_rejected',1)->get() as $rr)
                  <tr>
                    <td><a href="{{ route('view.client',['slug'=>$rr->client->slug]) }}">{{ $rr->client->name  }}</a></td>
                    <td>{{ App\User::find($rr->added_by)->name }}</td>
                    <td>{{ $rr->amount}}</td>
                    <td>{{ $rr->approved_rejected_amount}}</td>
                    <td>{{ $rr->refund_date }}</td>
                    <td>{{ $rr->reason }}</td>
                    <td>{{ App\User::find($rr->approved_rejected_by)->name }}</td>
                    <td>{{ $rr->approved_rejected_remarks }}</td>
                    <td>{{ $rr->approved_rejected_datetime }}</td>
                    <td>
                      @if($rr->approval_accounts_datetime)
                        Approved By <br>{{ App\User::find($rr->approval_accounts_by)->name }}
                      @else
                        <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#approve-by-accounts"
                                onclick="$('#approveByAccountsHtml').val({{$rr->id}});">Approve</button>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" class="text-center">No Records!</td>
                  </tr>
                @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="approve-by-accounts" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" >
        <div class="modal-dialog modal-dialog-centered" role="document" >
          <div class="modal-content" >
            <div class="modal-header bg-light" >
              <h5 class="modal-title" id="exampleModalLongTitle" ><strong>Approve</strong></h5>
            </div>
            <form action="{{ route('approve.accounts.refund.request') }}" method="post" >
              @csrf
              <div class="modal-body" >
                <input type="hidden" name="refund_request"  id="approveByAccountsHtml">

                <input type="checkbox" name="change_client_status" onclick="if(this.checked == true){ $('#toggleee').show() }else{ $('#toggleee').hide() }">Change Client Status <br>
                <div id="toggleee" style="display:none;">
                  &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="status" value="cancel" id="">Cancel
                  <input type="radio" name="status" value="forfiet" id="">Forfiet <br>
                </div><br>

                <div class="row">
                  <div class="col-md-6">
                    <label for="date_of_payment" class="pull-left">Date Of Payment</label>
                    <input type="date" name="date_of_payment" onchange="checkDate(this);"  class="form-control" required>
                  </div>
                  <div class="col-md-6">
                    <label for="amount" class="pull-left">Amount</label>
                    <input type="text" name="amount" class="form-control" required>  <br>
                  </div>
                </div>

                <div id="mode_of_payment"></div>
                <div id="mode_of_payment_box"></div>


                <label for="approval_accounts_remarks" class="pull-left">Remarks</label>
                <textarea name="approval_accounts_remarks" id="approval_accounts_remarks" class="form-control" required></textarea>
              </div>
              <div class="modal-footer bg-light" >
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <input type="submit" id="sub" class="btn btn-info" value="Approve"/>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="tab-pane fade show" id="rejected" role="tabpanel" aria-labelledby="home-tab">
      <div class="row">
        <div class="col-sm-12">
          <div class="card">
            <div class="card-header">
              <h3 class="text-center">Rejected Refund Request</h3>
            </div>
            <div class="card-body">
              <table class="table table-bordered">
                <thead>
                <tr>
                  <th>Client Name</th>
                  <th>Added By</th>
                  <th>Amount</th>
                  <th>Refund Date</th>
                  <th>Reason</th>
                  <th>Rejected By</th>
                  <th>Rejected Remarks</th>
                  <th>Rejected On</th>
                  <th>Client Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse(\App\RefundRequests::where('approved_rejected',2)->get() as $rr)
                  <tr>
                    <td><a href="{{ route('view.client',['slug'=>$rr->client->slug]) }}">{{ $rr->client->name  }}</a></td>
                    <td>{{ App\User::find($rr->added_by)->name }}</td>
                    <td>{{ $rr->amount}}</td>
                    <td>{{ $rr->refund_date }}</td>
                    <td>{{ $rr->reason }}</td>
                    <td>{{ App\User::find($rr->approved_rejected_by)->name }}</td>
                    <td>{{ $rr->approved_rejected_remarks }}</td>
                    <td>{{ $rr->approved_rejected_datetime }}</td>
                    <td>
                      @if($rr->approved_rejected_client_status_changed != null)
                        {{$rr->approved_rejected_client_status_changed}}
                      @else
                        {{__('No Change')}}
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="9" class="text-center">No Records!</td>
                  </tr>
                @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="tab-pane fade show" id="accounts-approved" role="tabpanel" aria-labelledby="home-tab">
      <div class="row">
        <div class="col-sm-12">
          <div class="card">
            <div class="card-header">
              <h3 class="text-center">Approved By Accounts</h3>
            </div>
            <div class="card-body">
              <table class="table table-bordered">
                <thead>
                <tr>
                  <th>Client Name</th>
                  <th>Added By</th>
                  <th>Amount Approved By Accounts</th>
                  <th>Date Of Payment</th>
                  <th>Reason</th>
                  <th>Approved By</th>
                  <th>Approved Remarks</th>
                  <th>Approved On</th>
                  <th>Client Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse(\App\RefundRequests::where('approval_accounts_datetime','!=',null)->orWhere('approval_accounts_datetime','!=',0)->get() as $rr)
                  <tr>
                    <td><a href="{{ route('view.client',['slug'=>$rr->client->slug]) }}">{{ $rr->client->name  }}</a></td>
                    <td>{{ App\User::find($rr->added_by)->name }}</td>
                    <td>{{ $rr->approval_accounts_amount}}</td>
                    <td>{{ $rr->date_of_payment }}</td>
                    <td>{{ $rr->reason }}</td>
                    <td>{{ App\User::find($rr->approval_accounts_by)->name }}</td>
                    <td>{{ $rr->approval_accounts_remarks }}</td>
                    <td>{{ $rr->approval_accounts_datetime }}</td>
                    <td>
                      @if($rr->approval_accounts_client_status_changed != null)
                        {{$rr->approval_accounts_client_status_changed}}
                      @else
                        {{__('No Change')}}
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="9" class="text-center">No Records!</td>
                  </tr>
                @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="tab-pane fade show" id="transactions" role="tabpanel" aria-labelledby="home-tab">
      <div class="row">
        <div class="col-sm-12">
          <div class="card">
            <div class="card-header">
              <ul class="nav nav-tabs tabs-design" id="myTab2" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#past" role="tab" aria-controls="profile">Past Transactions</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="profile-tab" data-toggle="tab" href="#upcoming" role="tab" aria-controls="profile">Upcoming Transactions</a>
                </li>
              </ul>
            </div>
            <div class="card-body">
              <div class="tab-content" id="myTab2Content">
                <div class="tab-pane active fade show" id="past" role="tabpanel" aria-labelledby="home-tab">
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="card">
                        <div class="card-header">
                          <h3 class="text-center">Past Transactions</h3>
                        </div>
                        <div class="card-body">
                          <table class="table table-bordered">
                            <thead>
                            <tr>
                              <th>Client Name</th>
                              <th>Amount</th>
                              <th>Date Of Payment</th>
                              <th>Mode Of Payment</th>
                              <th>Last Four Card Digits</th>
                              <th>Card Name</th>
                              <th>Bank Name</th>
                              <th>Cheque Number</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse(\App\RefundRequests::where('approval_accounts_datetime','!=',null)->Where('mode_of_payment','!=',null)->get() as $rr)
                              <tr>
                                <td><a href="{{ route('view.client',['slug'=>$rr->client->slug]) }}">{{ $rr->client->name  }}</a></td>
                                <td>{{ $rr->approval_accounts_amount}}</td>
                                <td>{{ $rr->date_of_payment }}</td>
                                <td>{{ $rr->mode_of_payment }}</td>
                                <td>{{ $rr->last_four_digits }}</td>
                                <td>{{ $rr->card_name }}</td>
                                <td>{{ $rr->bank_name }}</td>
                                <td>{{ $rr->cheque_number }}</td>
                              </tr>
                            @empty
                              <tr>
                                <td colspan="9" class="text-center">No Records!</td>
                              </tr>
                            @endforelse
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade show" id="upcoming" role="tabpanel" aria-labelledby="home-tab">
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="card">
                        <div class="card-header">
                          <h3 class="text-center">Upcoming Transactions</h3>
                        </div>
                        <div class="card-body">
                          <table class="table table-bordered">
                            <thead>
                            <tr>
                              <th>Client Name</th>
                              <th>Amount</th>
                              <th>Date Of Payment</th>
                              {{-- <th>Mode Of Payment</th>
                              <th>Last Four Card Digits</th>
                              <th>Card Name</th>
                              <th>Bank Name</th>
                              <th>Cheque Number</th> --}}
                            </tr>
                            </thead>
                            <tbody>
                            @forelse(\App\RefundRequests::where('approval_accounts_datetime','!=',null)->Where('mode_of_payment',null)->get() as $rr)
                              <tr>
                                <td><a href="{{ route('view.client',['slug'=>$rr->client->slug]) }}">{{ $rr->client->name  }}</a></td>
                                <td>{{ $rr->approval_accounts_amount}}</td>
                                <td>{{ $rr->date_of_payment }}</td>
                                {{-- <td>{{ $rr->mode_of_payment }}</td>
                                <td>{{ $rr->last_four_digits }}</td>
                                <td>{{ $rr->card_name }}</td>
                                <td>{{ $rr->bank_name }}</td>
                                <td>{{ $rr->cheque_number }}</td> --}}
                              </tr>
                            @empty
                              <tr>
                                <td colspan="9" class="text-center">No Records!</td>
                              </tr>
                            @endforelse
                            </tbody>
                          </table>
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
  <script>
    function paymentMode1(temp){
      var mode_of_payment = temp.value;
      if(mode_of_payment == 'debit_card'){
        var pay_type = 	  '    <div class="row"><div class="col-md-6">  '  +
          '                                   <label for="last_four_digits">Last Four Digits</label>  '  +
          '                                   <input type="text" name="last_four_digits" class="form-control" id="last_four_digits"/>  '  +
          '                               </div>  '  +
          '                               <div class="col-md-6">  '  +
          '                               <input type="hidden" name="bank_name">'+
          '                               <input type="hidden" name="cheque_number">'+
          '                                   <label for="card_name">Card Name/Description</label>  '  +
          '                                   <input type="text" name="card_name" class="form-control" id="card_name"/>  '  +
          '                              </div></div>  ' ;

      }
      if(mode_of_payment == "credit_card"){
        var pay_type = 	 '    <div class="row"><div class="col-md-6">  '  +
          '                                   <label for="last_four_digits">Last Four Digits</label>  '  +
          '                                   <input type="text" name="last_four_digits" class="form-control" id="last_four_digits"/>  '  +
          '                               </div>  '  +
          '                               <div class="col-md-6">  '  +
          '                               <input type="hidden" name="bank_name">'+
          '                               <input type="hidden" name="cheque_number">'+
          '                                   <label for="card_name">Card Name/Description</label>  '  +
          '                                   <input type="text" name="card_name" class="form-control" id="card_name"/>  '  +
          '                              </div></div>  ' ;

      }
      if(mode_of_payment == "bank"){
        var pay_type = 	 '    <div class="row"><div class="col-md-12">  '  +
          '                               <input type="hidden" name="cheque_number">'+
          '                               <input type="hidden" name="last_four_digits">'+
          '                               <input type="hidden" name="card_name">'+
          '                                   <label for="bank_name">Bank Name</label>  '  +
          '                                   <input type="text" name="bank_name" class="form-control" id="bank_name"/>  '  +
          '                               </div></div>  '  +
          '                                ' ;

      }
      if(mode_of_payment == "cheque"){
        var pay_type = 	  '   <div class="row"> <div class="col-md-12">  '  +
          '                                   <label for="cheque_number">Cheque Number</label>  '  +
          '                               <input type="hidden" name="last_four_digits">'+
          '                               <input type="hidden" name="card_name">'+
          '                               <input type="hidden" name="bank_name">'+
          '                                   <input type="text" name="cheque_number" class="form-control" id="cheque_number"/>  '  +
          '                               </div></div>  '  +
          '                                ' ;

      }
      if(mode_of_payment == "others" || mode_of_payment == 'online'){
        var pay_type = 	  '   <div class="row"> <div class="col-md-12">  '  +
          '                               <input type="hidden" name="last_four_digits">'+
          '                               <input type="hidden" name="card_name">'+
          '                               <input type="hidden" name="bank_name">'+
          '                                   <input type="hidden" name="cheque_number" class="form-control" id="cheque_number"/>  '  +
          '                               </div></div>  '  +
          '                                ' ;

      }
      if(mode_of_payment == ""){
        var pay_type = "";

      }
      $('#mode_of_payment_box').html(pay_type);

    }


    function checkDate(foo){
      var test = foo.value;
      var today = new Date();
      var dd = String(today.getDate()).padStart(2, '0');
      var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
      var yyyy = today.getFullYear();

      var today = yyyy + '-' + mm + '-' + dd;

      if(test <= today){
        var data = '<div class="row"><div class="col-md-6"><label for="mode_of_payment">Mode of Payment:</label>\n' +
          '                                              <select name="mode_of_payment" required onchange="paymentMode1(this)" class="form-control mode_of_payment1" required>\n' +
          '                                                  <option value="">---Select---</option>\n' +
          '                                                  <option value="credit_card">Credit Card</option>\n' +
          '                                                  <option value="debit_card">Debit Card</option>\n' +
          '                                                  <option value="bank">Bank Account</option>\n' +
          '                                                  <option value="cheque">Cheque</option>\n' +
          '                                                  <option value="online">Online</option>\n' +
          '                                                  <option value="others">Others</option>\n' +
          '                                                  </select>\n' +
          '                                            </div>\n' +
          '                                            <div class="payment_details_box1 col-md-6"></div></div>';

        $('#mode_of_payment').html(data);
      }else{
        $('#mode_of_payment').html('');
      }

    }
  </script>

@endsection

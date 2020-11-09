<div class="modal fade" id="addRefundRequest" tabindex="-1" role="dialog" aria-hidden="true" >
  <div class="modal-dialog modal-dialog-centered" role="document" >
    <div class="modal-content" >
      <div class="modal-header" >
        <h5 class="modal-title" id="exampleModalLongTitle" ><strong> Initiate {{ $client->name }}'s Refund</strong></h5>
        <span aria-hidden="true" data-dismiss="modal">&times;</span>
      </div>
      <form action="{{ route('add.refund.request',['slug'=>$client->slug]) }}" method="post" >
        @csrf
        <div class="modal-body" >
          <input type="hidden" name="id" value="{{$client->id}}">

          <label for="amount">Amount To be Refunded</label>
          <input type="text" class="form-control" name="amount" id="amount" max="{{ $client->paidAmount }}">
          <label for="through">Refund Request Through:</label>
          <input type="text" class="form-control" name="through">
          <label for="refund_date">Refund Date</label>
          <input type="date" class="form-control" name="refund_date" id="refund_date" value="{{\Carbon\Carbon::now()->toDateString()}}" max="{{ $client->paidAmount }}">
          <label for="reason" class="pull-left">Reason</label>
          <textarea name="reason" class="form-control" required></textarea>
        </div>
        <div class="modal-footer" >
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          <input type="submit" id="sub" class="btn btn-info" value="Request {{ $client->name }}'s Refund"/>
        </div>
      </form>
    </div>
  </div>
</div>

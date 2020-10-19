<div class="modal fade" id="newTransaction" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" >
  <div class="modal-dialog modal-dialog-centered" role="document" >
    <div class="modal-content" >
      <div class="modal-header" >
        <h5 class="modal-title" id="exampleModalLongTitle" ><strong>New Transaction!</strong></h5>
        <span aria-hidden="true" data-dismiss="modal">&times;</span>

      </div>
      <form action="" method="post" >
        @csrf
        <div class="modal-body" >
          <label for="add_on">Add On Transaction</label>
          <input type="checkbox" name="add_on"><br>
          <label for="add_on">DP Amount</label>
          <input type="checkbox" name="dp_amount"><br>
          <label for="mode_of_payment"> Mode of Payment:</label>
          <select name="mode_of_payment" id="mode_of_payment" class="form-control" required="">
            <option value="">-Select-</option>
            <option value="Credit Card">Credit Card</option>
            <option value="Debit Card">Debit Card</option>
            <option value="Cash">Cash</option>
            <option value="Cheque">Cheque</option>
            <option value="Paytm">Paytm</option>
            <option value="Online">Online</option>
            <option value="NACH">NACH</option>
          </select>
          <div id="card_details_box"></div>
          <label for="date_of_payment" class="pull-left">Date of Payment</label>
          <input type="text" id="datepicker" value="{{Carbon\Carbon::now()->toDateString()}}" name="date_of_payment" class="form-control" required />
          <label for="amount" class="pull-left">Amount</label>
          <input type="text" name="amount" class="form-control" required />
          <label for="bank" class="pull-left">Bank Name</label>
          <input type="text" name="bank" class="form-control" required />
          <label for="remarks" class="pull-left">Remarks</label>
          <input type="text" name="remarks" class="form-control" required />
        </div>
        <div class="modal-footer" >
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          <input type="submit" id="sub" class="btn btn-info" value="Add Transaction"/>
        </div>
      </form>
    </div>
  </div>
</div>



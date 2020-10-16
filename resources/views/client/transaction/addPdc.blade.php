<div class="modal fade" id="addPdc" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header ">
        <h5 class="modal-title"><strong>Add PDC</strong></h5>
        <span aria-hidden="true" data-dismiss="modal">×</span>
      </div>
      <form action="{{ route('add.pdc') }}" method="post">
        @csrf
        <div class="modal-body">
          <div class="row">
            <input type="hidden" value="{{ $client->id }}" name="client">
            <div class="col-md-12">
              <label for="cheque_number">Cheque Number</label>
              <input type="text" name="cheque_number" id="cheque_number" class="form-control" required="">
            </div>
            <div class="col-md-12">
              <label for="date_of_execution">Cheque Date</label>
              <input type="date" name="date_of_execution" id="date_of_execution" class="form-control" required="">
            </div>
            <div class="col-md-12">
              <label for="amount">Amount</label>
              <input type="number" name="amount" id="amount" class="form-control" required="">
            </div>
            <div class="col-md-12">
              <label for="micr_number">MICR Number</label>
              <input type="text" name="micr_number" id="micr_number" class="form-control" required="">
            </div>
            <div class="col-md-12">
              <label for="branch_name">Branch Name</label>
              <input type="text" name="branch_name" id="branch_name" class="form-control" required="">
            </div>
            <div class="col-md-12">
              <label for="branch_address">Branch Address</label>
              <input type="text" name="branch_address" id="branch_address" class="form-control" required="">
            </div>
            <div class="col-md-12">
              <label for="remarks0" class="pull-left" origfor="remarks">Remarks</label>
              <textarea name="remarks" class="form-control"></textarea>
            </div>
          </div>

        </div>
        <div class="modal-footer ">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-info">Add</button>
        </div>
      </form>
    </div>
  </div>
</div>

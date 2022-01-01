<div class="modal fade text-left" id="addOtherTransaction" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel33" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success">
        <h4 class="modal-title" id="myModalLabel33">Other Transaction</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('create.transaction.other',['clientId'=>$client->id]) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6  col-sm-12">
              <label>Payment Received On </label>
              <div class="form-group">
                <input type="date" placeholder="Payment Received On" name="paymentReceivedOn" class="form-control" required>
              </div>
            </div>
            <div class="col-md-6 col-sm-12">
              <label>Amount: </label>
              <div class="form-group">
                <input type="text" placeholder="Amount" class="form-control"  name="paymentAmount" onkeypress="return onlyNumberKey(event)" required>
              </div>
            </div>
            <div class="col-md-6 col-sm-12">
              <label>Mode Of Payment</label>
              <div class="form-group">
                <input type="text" placeholder="Mode Of Payment" class="form-control" name="modeOfPayment" required>
              </div>
            </div>

            <div class="col-md-2 col-sm-6">
              <fieldset>
                <div class="vs-checkbox-con vs-checkbox-success">
                  <input type="checkbox" name="paymentDownPayment">
                  <span class="vs-checkbox">
                      <span class="vs-checkbox--check">
                        <i class="vs-icon feather icon-check"></i>
                      </span>
                    </span>
                  <span class="">Down Payment</span>
                </div>
              </fieldset>
            </div>
            <div class="col-md-2 col-sm-6">
              <fieldset>
                <div class="vs-checkbox-con vs-checkbox-success">
                  <input type="checkbox" name="paymentAddOn">
                  <span class="vs-checkbox">
                      <span class="vs-checkbox--check">
                        <i class="vs-icon feather icon-check"></i>
                      </span>
                    </span>
                  <span class="">Add On</span>
                </div>
              </fieldset>
            </div>
            <div class="col-md-2 col-sm-6">
              <fieldset>
                <div class="vs-checkbox-con vs-checkbox-success">
                  <input type="checkbox" name="paymentBreatherCharges">
                  <span class="vs-checkbox">
                      <span class="vs-checkbox--check">
                        <i class="vs-icon feather icon-check"></i>
                      </span>
                    </span>
                  <span class="">Breather Charges</span>
                </div>
              </fieldset>
            </div>
            <div class="col-md-12 col-sm-12">
              <fieldset class="form-label-group">
                <textarea class="form-control" id="label-textarea" rows="3" placeholder="Remarks" name="paymentCardRemarks" required></textarea>
                <label for="label-textarea">Remarks</label>
              </fieldset>
            </div>
          </div>

          <div id="paymentFor">
            <div class="row">
              <div class="col-md-12">
                  <span class="alert-primary">
                    If this EMI was paid for multiple months then add those months below.
                  </span>
                {{--                <button class="btn btn-primary btn-sm" id="addMonth">Add Month</button>--}}
                <input type="button" class="btn btn-primary btn-sm" id="addMonth" value="Add Month">
              </div>
            </div>
            <div id="paymentForMonths">
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Add</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    var addButton = $('#addMonth'); //Add button selector
    var wrapper = $('#paymentForMonths'); //Input field wrapper
    var fieldHTML = '              <div class="row">\n' +
      '                <div class="col-md-3">\n' +
      '                  <label>Month</label>\n' +
      '                  <div class="form-group">\n' +
      '                    <select name="paymentForMonth[]" class="form-control" required>\n' +
      '                      <option value="01">January</option>\n' +
      '                      <option value="02">February</option>\n' +
      '                      <option value="03">March</option>\n' +
      '                      <option value="04">April</option>\n' +
      '                      <option value="05">May</option>\n' +
      '                      <option value="06">June</option>\n' +
      '                      <option value="07">July</option>\n' +
      '                      <option value="08">August</option>\n' +
      '                      <option value="09">September</option>\n' +
      '                      <option value="10">October</option>\n' +
      '                      <option value="11">November</option>\n' +
      '                      <option value="12">December</option>\n' +
      '                    </select>\n' +
      '                  </div>\n' +
      '                </div>\n' +
      '                <div class="col-md-3">\n' +
      '                  <label>Year</label>\n' +
      '                  <div class="form-group">\n' +
      '                    <select name="paymentForYear[]" class="form-control" required>\n' +
      '                      <option value="2020">2020</option>\n' +
      '                      <option value="2019">2019</option>\n' +
      '                      <option value="2018">2018</option>\n' +
      '                      <option value="2017">2017</option>\n' +
      '                    </select>\n' +
      '                  </div>\n' +
      '                </div>\n' +
      '                    <span id="removeThis">Remove</span>\n' +
      '                </div>\n' +
      '              </div>\n'; //New input field html

    //Once add button is clicked
    $(addButton).click(function () {
      $(wrapper).append(fieldHTML); //Add field html
    });

    //Once remove button is clicked
    $(wrapper).on('click', '#removeThis', function (e) {
      e.preventDefault();
      $(this).parent('div').remove(); //Remove field html
    });
  })
</script>

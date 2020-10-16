<div class="modal fade text-left" id="disableNach" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel33" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success">
        <h4 class="modal-title" id="myModalLabel33">Disable NACH</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('disable.nach') }}" method="post" >
        @csrf
        <div class="modal-body" >
          <div class="row">
            <div class="col-md-12">
              <input type="checkbox" name="permanent" id="permanent" value="1"> Disable NACH Permanently<br>
              <input type="hidden" name="client" id="client" value="{{ $client->id }}">
            </div>
          </div>
          <div class="row" id="permanentDisabled">
            <div class="col-md-6">
              <label for="month">Month</label>
              <select name="month" id="" class="form-control" >
                <option value="">--Select Month--</option>
                <option value="01">January</option>
                <option value="02">February</option>
                <option value="03">March</option>
                <option value="04">April</option>
                <option value="05">May</option>
                <option value="06">June</option>
                <option value="07">July</option>
                <option value="08">August</option>
                <option value="09">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="year">Year</label>
              <select name="year" id="" class="form-control">
                <option value="">--Select Year--</option>
                <option value="{{Carbon\Carbon::now()->year}}">{{Carbon\Carbon::now()->year}}</option>
                <option value="{{Carbon\Carbon::now()->addYear(1)->year}}">{{Carbon\Carbon::now()->addYear(1)->year}}</option>
              </select>
            </div>
          </div>
          <label for="remarks" class="pull-left">Remarks</label>
          <textarea name="remarks" class="form-control" required></textarea>
        </div>
        <div class="modal-footer" >
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
{{--          <input type="submit" id="sub" class="btn btn-info" value="Disable"/>--}}
          <button type="submit" class="btn btn-primary">Disable</button>
        </div>
      </form>

    </div>
  </div>
</div>

<script>
  $(function () {
    $("#permanent").click(function () {
      if ($(this).is(":checked")) {
        $("#permanentDisabled").hide();
      } else {
        $("#permanentDisabled").show();
      }
    });
  });
</script>

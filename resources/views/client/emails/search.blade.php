<div class="modal fade" id="searchMail" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Search Mail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('email.search') }}" method="POST">
        @csrf
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <label for="mailSubject">Subject</label>
            <input type="text" name="mailSubject" class="form-control">
          </div>
{{--          <div class="col-md-4">--}}
{{--            <label for="mailTo">To</label>--}}
{{--            <input type="text" name="mailTo" class="form-control">--}}
{{--          </div>--}}
          <div class="col-md-6">
            <label for="mailFrom">From Email</label>
            <input type="text" name="mailFrom" class="form-control">
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <label for="mailDate">Date</label>
            <input type="date" name="mailDate" class="form-control">
          </div>
{{--          <div class="col-md-6">--}}
{{--            <label for="mailContains">Contains</label>--}}
{{--            <input type="text" name="mailContains" class="form-control" placeholder="Text Inside Mail">--}}
{{--          </div>--}}
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
      </div>
      </form>

    </div>
  </div>
</div>

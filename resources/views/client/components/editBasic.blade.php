<div class="modal fade" id="editBasicClientDetails" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit {{ $client->name }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('update.basicDetails',['clientId'=>$client->id]) }}" method="POST">
        @csrf
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <label for="clientName">Client Name</label>
            <input type="text" name="clientName" value="{{ $client->name }}" class="form-control" required>
          </div>
          <div class="col-md-12">
            <label for="enrollmentDate">Enrollment Date</label>
            <input type="date" name="enrollmentDate" value="{{ $client->latestPackage->enrollmentDate }}" class="form-control" required>
          </div>
          <div class="col-md-12">
            <label for="address">Address</label>
            <input type="text" name="address" value="{{ $client->address }}" class="form-control" required>
          </div>
          <div class="col-md-12">
            <label for="email">Email</label>
            <input type="text" name="email" value="{{ $client->email }}" class="form-control" required>
          </div>
          <div class="col-md-12">
            <label for="phone">Phone</label>
            <input type="number" name="phone" value="{{ $client->phone }}" class="form-control" required>
          </div>
          <div class="col-md-12">
            <label for="phone">Alternate Phone</label>
            <input type="number" name="altPhone" value="{{ $client->altPhone }}" class="form-control">
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

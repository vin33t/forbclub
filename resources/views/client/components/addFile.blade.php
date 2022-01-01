<div class="modal fade" id="addDocumentFile" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add File for  {{ $client->name }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('add.legal.notice',['clientId'=>$client->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <label for="noticeReason">File/Document</label>
              <input type="file" name="file" class="form-control" required>
            </div>
            <div class="col-md-12">
              <label for="noticeDocument">Notice Document</label>
              <input type="file" name="noticeDocument" class="form-control">
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Add File</button>
          </div>
      </form>
    </div>
  </div>
</div>

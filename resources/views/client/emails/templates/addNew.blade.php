<div class="modal fade" id="addNewTemplate" tabindex="-1" role="dialog" aria-labelledby="addNewTemplate" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Add New Template</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('email.templates.create') }}" method="POST" id="createNewTemplate">
        @csrf
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <label for="templateSubject">Template Subject</label>
            <input type="text" name="templateSubject" class="form-control" required>
          </div>
          <div class="col-md-12">
            <hr>
            <textarea name="templateContent" id="templateContent" style="display: none"></textarea>
            <div id="email-container">
              <div class="editor" data-placeholder="Message">
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Add</button>
      </div>
      </form>
    </div>
  </div>
</div>

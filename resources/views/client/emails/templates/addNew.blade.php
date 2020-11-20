<div class="modal fade" id="addNewTemplate" tabindex="-1" role="dialog" aria-labelledby="addNewTemplate" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Add New Template</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <label for="templateName">Template Name</label>
            <input type="text" name="templateName" class="form-control" required>
          </div>
          <div class="col-md-12">
            <hr>

            <label for="templateSubject">Template Subject</label>
            <input type="text" name="templateSubject" class="form-control" required>
          </div>
          <div class="col-md-12">
            <div id="email-container">
              <div class="editor" data-placeholder="Message">
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Add</button>
      </div>
    </div>
  </div>
</div>

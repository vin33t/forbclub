<div class="modal fade text-left" id="addPackageBenefitModal" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel33" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success">
        <h4 class="modal-title" id="myModalLabel33">Add Package Benefit</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('add.client.package.benefit',['currentPackageId'=>$client->latestPackage->id,'clientId'=>$client->id]) }}" method="POST" class="m-1">
        @csrf
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6  col-sm-12">
                <div class="form-label-group">
                <input type="text" placeholder="Benefit Name" name="benefitName" class="form-control" required>
              <label>Benefit Name</label>
              </div>
            </div>
            <div class="col-md-6 col-sm-12">
              <div class="form-label-group">
              <input type="text" placeholder="Benefit Description" class="form-control"  name="benefitDescription" required>
              <label>Benefit Description </label>
              </div>
            </div>
            <div class="col-md-6 col-sm-12">
              <div class="form-label-group">
                <textarea class="form-control" id="label-textarea" rows="3" placeholder="Benefit Conditions" name="benefitConditions" required></textarea>
                <label for="label-textarea">Benefit Conditions</label>
              </div>
            </div>


            <div class="col-md-6 col-sm-12">
              <fieldset class="form-label-group">
                <input type="date" class="form-control" name="benefitValidity" required>
                <label for="label-textarea">Benefit Validity</label>
              </fieldset>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Add Benefit</button>
        </div>
      </form>
    </div>
  </div>
</div>

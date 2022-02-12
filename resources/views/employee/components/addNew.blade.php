<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addNewEmployee">Add New Employee</button>
<div class="modal fade" id="addNewEmployee" tabindex="-1" role="dialog" aria-labelledby="addNewEmployeeTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Add New Employee</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('add.new.employee') }}" method="POST">
        @csrf
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <label for="name" id="name">Employee Name</label>
            <input type="text" placeholder="Name" name="name" id="name" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label for="phone" id="phone">Employee Phone</label>
            <input type="text" placeholder="Phone" name="phone" id="phone" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label for="email" id="email">Employee Email</label>
            <input type="text" placeholder="email" name="email" id="email" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label for="department" id="department">Employee Department</label>
            <select  name="department" id="department" class="form-control" required>\
              <option value="">--Select Department--</option>
              <option value="MRD">MRD</option>
              <option value="Accounts">Accounts</option>
              <option value="Marketing">Marketing</option>
            </select>
          </div>

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Add Employee</button>
      </div>
      </form>
    </div>
  </div>
</div>

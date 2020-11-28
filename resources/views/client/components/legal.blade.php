
    <div class="col-md-12">
      <hr>
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Legal Notices</h4>
        </div>
        <div class="card-content">
          <div class="card-body card-dashboard">
            <div class="table-responsive">
              <table class="table zero-configuration">
                <thead>
                <tr>
                  <th>Notice Reason</th>
                  <th>Notice Date</th>
                  <th>Notice Description</th>
                  <th>Hearing Date</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($client->legalNotice as $notice)
                  <tr>
                    <td>{{ $notice->noticeReason }}</td>
                    <td>{{ \Carbon\Carbon::parse($notice->noticeDate)->format('d-m-Y') }}</td>
                    <td>{{ $notice->noticeDescription }}</td>
                    <td>{{ \Carbon\Carbon::parse($notice->hearingDate)->format('d-m-Y') }}</td>
                    <td>
                      <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editLegalNotice{{ $notice->id }}">
                        <i class="fa fa-edit"></i>
                      </button>
                      <div class="modal fade" id="editLegalNotice{{$notice->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLabel">Edit Notice</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <form action="{{ route('edit.legal.notice',['noticeId'=>$notice->id]) }}" method="POST">
                              @csrf
                              <div class="modal-body">
                                <div class="row">
                                  <div class="col-md-12">
                                    <label for="noticeReason">Notice Reason</label>
                                    <input type="text" name="noticeReason" class="form-control" required value="{{ $notice->noticeReason }}">
                                  </div>
                                  <div class="col-md-12">
                                    <label for="noticeDate">Notice Date</label>
                                    <input type="date" name="noticeDate" class="form-control" required value="{{ $notice->noticeDate }}">
                                  </div>
                                  <div class="col-md-12">
                                    <label for="hearingDate">Hearing Date</label>
                                    <input type="date" name="hearingDate" class="form-control" required value="{{ $notice->hearingDate }}">
                                  </div>
                                  <div class="col-md-12">
                                    <label for="noticeDescription">Notice Description</label>
                                    <textarea name="noticeDescription" cols="30" rows="10" class="form-control">{{ $notice->noticeDescription }}</textarea>
                                  </div>

                                  <div class="col-md-12">
                                    <label for="noticeDocument">Notice Document</label>
                                    <input type="file" name="noticeDocument" class="form-control">
                                  </div>

                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                  <button type="submit" class="btn btn-primary">Edit Notice</button>
                                </div>
                            </form>
                          </div>
                        </div>
                      </div>

                    </td>
                  </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                  <th>Notice Reason</th>
                  <th>Notice Date</th>
                  <th>Notice Description</th>
                  <th>Hearing Date</th>
                  <th>Action</th>
                </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

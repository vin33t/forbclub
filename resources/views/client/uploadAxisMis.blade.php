@extends('layouts/contentLayoutMaster')

@section('title', 'Upload Axis Mis')

@section('content')
  <div class="col-lg-6 col-12">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <form action="{{ route('upload.axis.mis.file') }}" method="POST">
            @csrf
            <div class="card-body">
              <h4>Upload AXIS MIS File</h4>
              <hr>
              <div class="row">
                <div class="col-md-6">
                  <label for="axisMisFile">Axis Mis File</label>
                  <input type="file" class="form-control" name="axisMisFile" id="axisMisFile" required>
                </div>
              </div>

            </div>

            <div class="card-footer">
              <button class="btn btn-primary" type="submit">Upload</button>
            </div>
          </form>
        </div>
      </div>


    </div>
  </div>

@endsection

@section('page-script')
  <!-- Page js files -->
  <script src="{{ asset(mix('js/scripts/modal/components-modal.js')) }}"></script>
@endsection

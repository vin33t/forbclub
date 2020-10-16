@extends('layouts/contentLayoutMaster')

@section('title', 'Upload Trasactions')

@section('content')
  <div class="col-lg-6 col-12">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <form action="{{ route('upload.transaction.file') }}" method="POST">
            @csrf
            <div class="card-body">
              <h4>Upload Client Transction File</h4>
              <hr>
              <div class="row">
                <div class="col-md-6">
                  <label for="transactionFile">Transaction File</label>
                  <input type="file" class="form-control" name="transactionFile" id="transactionFile" required>
                </div>
                <div class="col-md-6">
                  <label for="bank">Bank</label>
                  <select name="bank" id="bank" class="form-control" required>
                    <option value="">--Select Bank--</option>
                    <option value="Yes">Yes Bank</option>
                    <option value="Axis">Axis Bank</option>
                  </select>
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

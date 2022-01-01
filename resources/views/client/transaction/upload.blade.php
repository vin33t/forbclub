@extends('layouts/contentLayoutMaster')

@section('title', 'Upload Trasactions')

@section('content')
  <div class="row">
    @if(session()->has('message'))
  <div class="col-md-12">
      <div class="alert alert-success">
        {{ session()->get('message') }}
      </div>
  </div>
    @endif
    @if($errors->any())
      <div class="col-md-12">
        <div class="alert alert-danger" role="alert">
          Error While Uploading File <br>
          <ul>
            @foreach ($errors->all() as $error)
              <li>
                @if($error == 'A non well formed numeric value encountered')
                  AMOUNT Column has INTEGER Values stored as TEXT. Convert them to numbers first <a
                    href="https://support.microsoft.com/en-us/office/fix-text-formatted-numbers-by-applying-a-number-format-6599c03a-954d-4d83-b78a-23af2c8845d0"
                    target="_blank">CLICK HERE</a> to know more
                  @else
                  {{$error}}
                @endif
              </li>
            @endforeach
          </ul>
        </div>
      </div>
    @endif
    <div class="col-lg-6 col-12">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <form action="{{ route('upload.transaction.file') }}" method="POST" enctype="multipart/form-data">
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
    <div class="col-lg-6 col-12">
      <div class="row">
        <div class="col-md-12">
          <div class="card">

            <div class="card-body">
              <h4>Download Sample Files</h4>
              <hr>
              <div class="row">
                <div class="col-md-6">
                  <a href="https://www.dropbox.com/scl/fi/gc53kcomn42aasg6ntf31/axis_sample_nach.xls?dl=1"
                     target="_blank"><h3>Axis Bank</h3></a>
                </div>
                <div class="col-md-6">
                  <a href="https://www.dropbox.com/scl/fi/lnuz2lcasw6wigeqqxmyh/yes_sample_nach.xlsx?dl=1"
                     target="_blank"><h3>Yes Bank</h3></a>
<br>Instructions to Upload Yes Bank Transaction File
 <ul>
<li><strong>VALUEDATE</strong> column should contain date in <strong>mm-dd-yyyy</strong> format.</li>
<li><strong>AMOUNT</strong> column's data should be converted to integer.</li>
</ul>
                </div>
              </div>

            </div>
          </div>
        </div>


      </div>
    </div>
  </div>
@endsection

@section('page-script')
  <!-- Page js files -->
  <script src="{{ asset(mix('js/scripts/modal/components-modal.js')) }}"></script>
@endsection

@extends('layouts/contentLayoutMaster')

@section('title', 'Download Axis Mis')

@section('content')
  <div class="col-lg-6 col-12">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <form action="{{ route('download.axis.mis.file') }}" method="POST">
            @csrf
          <div class="card-body">
            <h4>Download AXIS MIS File</h4>
            <hr>
              <div class="row">
                <div class="col-md-6">
                  <label for="month">Month</label>
                  <select name="month" id="month" class="form-control" required>
                    <option value="">--Select Month--</option>
                    <option value="january">January</option>
                    <option value="february">February</option>
                    <option value="march">March</option>
                    <option value="april">April</option>
                    <option value="may">May</option>
                    <option value="june">June</option>
                    <option value="july">July</option>
                    <option value="august">August</option>
                    <option value="september">September</option>
                    <option value="october">October</option>
                    <option value="november">November</option>
                    <option value="december">December</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="year">Year</label>
                  <select name="year" id="year" class="form-control" required>
                    <option value="">--Select Month--</option>
                    <option value="2020">2020</option>
                    <option value="2021">2021</option>

                  </select>
                </div>
              </div>

          </div>

          <div class="card-footer">
            <button class="btn btn-primary" type="submit">Download</button>
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

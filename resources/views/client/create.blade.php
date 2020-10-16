
@extends('layouts/contentLayoutMaster')

@section('title', 'Create Client')
@section('vendor-style')
  <!-- vendor css files -->
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/ui/prism.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/file-uploaders/dropzone.min.css')) }}">
@endsection
@section('page-style')
  <link rel="stylesheet" href="{{ asset(mix('css/plugins/forms/validation/form-validation.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/plugins/file-uploaders/dropzone.css')) }}">
@endsection

@section('content')
  @if (count($errors) > 0)
    <div class = "alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
  <form action="{{ route('create.client') }}" method="POST">
    @csrf
  <section id="basic-horizontal-layouts">
    <div class="row match-height">
      <div class="col-md-6 col-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Personal Information</h4>
          </div>
          <div class="card-content">
            <div class="card-body">
                  <div class="row">
                    <div class="col-12">
                      <div class="form-group row">
                        <div class="col-md-4">
                          <span>Name</span>
                        </div>
                        <div class="col-md-8">
                          <div class="position-relative has-icon-left">
                          <input type="text" id="client-name" class="form-control" name="clientName" placeholder="Client Name" required data-validation-required-message="Please Enter a Role Name" value="{{ old('clientName') }}">
                          <div class="form-control-position">
                            <i class="feather icon-user"></i>
                          </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group row">
                        <div class="col-md-4">
                          <span>Email</span>
                        </div>
                        <div class="col-md-8">
                          <div class="position-relative has-icon-left">
                          <input type="email" id="email-id" class="form-control" name="clientEmail" placeholder="Client Email" value="{{ old('clientEmail') }}">
                            <div class="form-control-position">
                              <i class="feather icon-mail"></i>
                            </div>
                        </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group row">
                        <div class="col-md-4">
                          <span>Phone</span>
                        </div>
                        <div class="col-md-8">
                          <div class="position-relative has-icon-left">
                          <input type="number" id="contact-info" class="form-control" name="clientPhone" placeholder="Client Phone Number" required>
                            <div class="form-control-position">
                              <i class="feather icon-smartphone"></i>
                            </div>
                          </div>
                          </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group row">
                        <div class="col-md-4">
                          <span>Alternate Phone</span>
                        </div>
                        <div class="col-md-8">
                          <div class="position-relative has-icon-left">
                          <input type="number" id="contact-info" class="form-control" name="clientAltPhone" placeholder="Client Alternate Phone Number">
                            <div class="form-control-position">
                              <i class="feather icon-smartphone"></i>
                            </div>
                          </div>
                          </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group row">
                        <div class="col-md-4">
                          <span>Date Of Birth</span>
                        </div>
                        <div class="col-md-8">
                          <input type="date" id="birthDate" class="form-control" name="clientBirthDate" placeholder="Date of Birth">
                        </div>
                      </div>
                    </div>
                  </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Enrollment Information</h4>
          </div>
          <div class="card-content">
            <div class="card-body">
                <div class="form-body">
                  <div class="row">
                    <div class="col-12">
                      <div class="form-group row">
                        <div class="col-md-4">
                          <span>Date Of Enrollment</span>
                        </div>
                        <div class="col-md-8">
                          <div class="position-relative has-icon-left">
                            <input type="date" class="form-control" name="productEnrollmentDate" placeholder="Date of Enrollment" required>
                            <div class="form-control-position">
                              <i class="feather icon-calendar"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group row">
                        <div class="col-md-4">
                          <span>Maf No</span>
                        </div>
                        <div class="col-md-8">
                          <div class="position-relative has-icon-left">
                            <input type="email" id="email-icon" class="form-control" name="productMafNo" placeholder="Maf No" required>
                            <div class="form-control-position">
                              <i class="feather icon-hash"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group row">
                        <div class="col-md-4">
                          <span>Fclp Id</span>
                        </div>
                        <div class="col-md-8">
                          <div class="position-relative has-icon-left">
                            <input type="number" id="contact-icon" class="form-control" name="productFclpId" placeholder="Fclp Id" required>
                            <div class="form-control-position">
                              <i class="feather icon-hash"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group row">
                        <div class="col-md-4">
                          <span>Sale By</span>
                        </div>
                        <div class="col-md-8">
                          <div class="position-relative has-icon-left">
                            <input type="text" id="pass-icon" class="form-control" name="productSaleBy" placeholder="Sale By" required>
                            <div class="form-control-position">
                              <i class="feather icon-user"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group row">
                        <div class="col-md-4">
                          <span>Branch</span>
                        </div>
                        <div class="col-md-8">
                          <div class="position-relative has-icon-left">
                            <input type="text" id="pass-icon" class="form-control" name="productBranch" placeholder="Branch" required>
                            <div class="form-control-position">
                              <i class="feather icon-home"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section id="multiple-column-form">
    <div class="row match-height">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Product Information</h4>
          </div>
          <div class="card-content">
            <div class="card-body">
                <div class="form-body">
                  <div class="row">
                    <div class="col-md-6 col-12">
                      <div class="form-group">
                        <label for="product-type">Product Type</label>
                        <select id="product-type" class="select2 form-control" name="productType" required>
                          <option value="fclp">FCLP</option>
                          <option value="fcv">FCV</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6 col-12">
                      <div class="form-group">
                        <label for="product-name">Product Name</label>
                        <input type="text" id="product-name" class="form-control" placeholder="Product Name" name="productName" required>
                      </div>
                    </div>
                    <div class="col-md-6 col-12">
                      <div class="form-label-group">
                        <input type="number" id="product-tenure" class="form-control" placeholder="Product Tenure" name="productTenure" required>
                        <label for="product-tenure">Product Tenure</label>
                      </div>
                    </div>
                    <div class="col-md-6 col-12">
                      <div class="form-label-group">
                        <input type="number" id="product-cost" class="form-control" name="productCost" placeholder="Product Cost" required>
                        <label for="product-cost">Product Cost</label>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">File Upload</h4>
          </div>
          <div class="card-content">
            <div class="card-body">
              <div class="row">
                <div class="col-lg-6 col-md-12">
                  <fieldset class="form-group">
                    <label for="basicInputFile">MAF</label>
                    <div class="custom-file">
                      <input type="file" class="custom-file-input" id="inputGroupFile01">
                      <label class="custom-file-label" for="inputGroupFile01">Choose MAF File</label>
                    </div>
                  </fieldset>
                </div>
                <div class="col-lg-6 col-md-12">
                  <fieldset class="form-group">
                    <label for="basicInputFile">Photo</label>
                    <div class="custom-file">
                      <input type="file" class="custom-file-input" id="inputGroupFile01">
                      <label class="custom-file-label" for="inputGroupFile01">Choose Enrollment Photo</label>
                    </div>
                  </fieldset>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <div class="row">
    <div class="col-md-12 text-right">
      <div class="card">
        <div class="card-body">
        <button type="submit" class="btn bg-gradient-success mr-1 mb-1">Save Client</button>
        </div>
      </div>
    </div>
  </div>
  </form>
@endsection
@section('vendor-script')
  <!-- vendor files -->
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/validation/jqBootstrapValidation.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/dropzone.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/ui/prism.min.js')) }}"></script>
@endsection
@section('page-script')
  <!-- Page js files -->
  <script src="{{ asset(mix('js/scripts/forms/select/form-select2.js')) }}"></script>
  <script src="{{ asset(mix('js/scripts/extensions/dropzone.js')) }}"></script>

@endsection


<div class="col-lg-3 col-12">
  <div class="card">
    <div class="card-header">
      <h4>About</h4>
      <i class="feather icon-more-horizontal cursor-pointer"></i>
    </div>
    @php
      $package = $client->latestPackage;
    @endphp
    <example-component></example-component>
    <div class="card-body">
      <p></p>
      <div class="mt-1">
        <h6 class="mb-0">Name:</h6>
        <p>{{ $client->name }}</p>
      </div>
      <div class="mt-1">
        <h6 class="mb-0">Enrolled On:</h6>
        <p>{{ \Carbon\Carbon::parse($package->enrollmentDate)->format('d M, Y') }}</p>
      </div>
      <div class="mt-1">
        <h6 class="mb-0">Membership Expiring On:</h6>
        <p>{{  \Carbon\Carbon::parse($package->enrollmentDate)->addYears($package->productTenure)->format('d M, Y') }}</p>
      </div>
      <div class="mt-1">
        <h6 class="mb-0">Address:</h6>
        <p>{{ $client->address }}</p>
      </div>
      <div class="mt-1">
        <h6 class="mb-0">Email:</h6>
        <p>{{ $client->email }}</p>
      </div>
      <div class="mt-1">
        <h6 class="mb-0">Phone:</h6>
        <p>{{ $client->phone }}</p>
      </div>
      <div class="mt-1">
        <h6 class="mb-0">Product:</h6>
        <p>{{ $package->productType }} | {{ $package->productName }} | {{ $package->productTenure }} Years | {{ inr($package->productCost) }}</p>
      </div>
      <div class="mt-1">
        <h6 class="mb-0">Status:</h6>
        <p>{{ $package->status }}
          @if($user->employee)
            <button class="btn btn-sm btn-primary"  data-toggle="modal" data-target="#updateStatus" >Update</button>
          @endif
        </p>
        @if($user->employee)

        <div class="modal fade" id="updateStatus" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <form action="{{ route('update.status',['id'=>$package->id]) }}" method="post" enctype="multipart/form-data">
              @csrf
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Update Status</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="row">
                    <div class="col-md-12">
                      <label for="">Status</label>
                      <select name="status" id="" class="form-control" required>
                        <option value="">--SELECT--</option>
                        <option value="Active">Active</option>
                        <option value="Breather">Breather</option>
                        <option value="Cancelled">Cancelled</option>
                      </select>
                    </div>
                    <div class="col-md-12">
                      <label for="">Remarks</label>
                      <textarea name="remarks" cols="30" rows="10" class="form-control"></textarea>
                    </div>
                  </div>

                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Update</button>
                </div>
              </div>
            </form>
          </div>
        </div>
@endif
      </div>

      <div class="mt-1">
        <h6 class="mb-0">MAF</h6>
        <p>
          @if($client->document)
          <a href="{{ $client->document->url }}" target="_blank"><button class="btn btn-primary btn-sm">View Maf</button></a>
          @else
            @if($user->employee)
              <button data-toggle="modal" data-target="#uploadMaf" class="btn btn-warning">Upload MAF Now</button>
             <div class="modal fade" id="uploadMaf" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <form action="{{ route('upload.maf') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Upload MAF</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <input type="file" class="form-control" name="maf" id="maf" required  accept="application/pdf"/>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        @else
          {{ __('MAF NOT UPLOADED') }}
        @endif
          @endif
        </p>
      </div>
      {{--              <div class="mt-1">--}}
      {{--                <button type="button" class="btn btn-sm btn-icon btn-primary mr-25 p-25"><i class="feather icon-facebook"></i></button>--}}
      {{--                <button type="button" class="btn btn-sm btn-icon btn-primary mr-25 p-25"><i class="feather icon-twitter"></i></button>--}}
      {{--                <button type="button" class="btn btn-sm btn-icon btn-primary p-25"><i class="feather icon-instagram"></i></button>--}}
      {{--              </div>--}}
    </div>
  </div>

  @if($user->employee)

  {{--sale BY--}}
  <div class="card">
    <div class="card-header">
      <h4 class="card-title">Sale By</h4>
    </div>
    <div class="card-body suggested-block">
      <div class="d-flex justify-content-start align-items-center mb-1">
        <div class="avatar mr-50">
          <img src="{{ avatar($package->saleBy) }}" alt="avtar img holder" height="35"
               width="35">
        </div>
        <div class="user-page-info">
          <p>{{ $package->saleBy }}</p>
        </div>
      </div>
    </div>
  </div>

{{--sale manager--}}
  <div class="card">
    <div class="card-header">
      <h4 class="card-title">Sale Manager</h4>
    </div>
    <div class="card-body suggested-block">
      <div class="d-flex justify-content-start align-items-center mb-1">
        <div class="avatar mr-50">
          <img src="{{ avatar($package->saleManager) }}" alt="avtar img holder" height="35"
               width="35">
        </div>
        <div class="user-page-info">
          <p>{{ $package->saleManager }}</p>
        </div>
      </div>
    </div>
  </div>
@endif
  <div class="card">
    <div class="card-header">
      <h4 class="card-title">EKIT
        @if($user->employee)
          <a href="{{ route('client.sendekit',['slug'=>$client->slug]) }}"><button class="btn btn-sm btn-primary">Send</button></a>
      @endif
      </h4>
    </div>
    <div class="card-body suggested-block">
      <div class="d-flex justify-content-start align-items-center mb-1">
        <div class="avatar mr-50">
          <img src="{{ avatar('Certificate') }}" alt="avtar img holder" height="35"
               width="35">
        </div>
        <div class="user-page-info">
          <p><a href="{{ route('client.certificate',['slug'=>$client->slug]) }}" target="_blank">Certificate</a></p>
        </div>
      </div>
      <div class="d-flex justify-content-start align-items-center mb-1">
        <div class="avatar mr-50">
          <img src="{{ avatar('Welcome Letter') }}" alt="avtar img holder" height="35"
               width="35">
        </div>
        <div class="user-page-info">
          <p><a href="{{ route('client.welcomeletter',['slug'=>$client->slug]) }}" target="_blank">Welcome Letter</a></p>
        </div>
      </div>
      <div class="d-flex justify-content-start align-items-center mb-1">
        <div class="avatar mr-50">
          <img src="{{ avatar('E MAF') }}" alt="avtar img holder" height="35"
               width="35">
        </div>
        <div class="user-page-info">
          <p><a href="{{ route('client.emaf',['slug'=>$client->slug]) }}" target="_blank">EMAF</a></p>
        </div>
      </div>
    </div>
  </div>


  <div class="card">
    <div class="card-header">
      <h4 class="card-title">Last Login @if(!$client->user)
          <form action="{{ route('create.client.login',['slug'=>$client->slug]) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm btn-primary">Create Login</button>
          </form>
        @endif</h4>
    </div>

    <div class="card-body suggested-block">
      <div class="d-flex justify-content-start align-items-center mb-1">
        <div class="user-page-info">
          <p>@if($client->login) {{ $client->lastLogin == NULL ? $client->lastLogin : 'Login Never Used'}} @else {{ __('Login Not Created') }} @endif</p>
        </div>
      </div>

    </div>
  </div>



</div>

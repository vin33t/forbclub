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
      @if($client->document)
      <div class="mt-1">
        <h6 class="mb-0">View Maf</h6>
        <p>
          <a href="{{ $client->document->url }}" target="_blank"><button class="btn btn-primary btn-sm">View Maf</button></a>
        </p>
      </div>
      @endif
      {{--              <div class="mt-1">--}}
      {{--                <button type="button" class="btn btn-sm btn-icon btn-primary mr-25 p-25"><i class="feather icon-facebook"></i></button>--}}
      {{--                <button type="button" class="btn btn-sm btn-icon btn-primary mr-25 p-25"><i class="feather icon-twitter"></i></button>--}}
      {{--                <button type="button" class="btn btn-sm btn-icon btn-primary p-25"><i class="feather icon-instagram"></i></button>--}}
      {{--              </div>--}}
    </div>
  </div>

  @if($package->saleBy)
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
  @endif


</div>

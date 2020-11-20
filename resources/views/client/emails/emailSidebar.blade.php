
@section('content-sidebar')
  @include('client.emails.search')
  <div class="sidebar-content email-app-sidebar d-flex">
        <span class="sidebar-close-icon">
            <i class="feather icon-x"></i>
        </span>
    <div class="email-app-menu">
      <div class="form-group form-group-compose text-center compose-btn">
        <button type="button" class="btn btn-primary btn-block my-2" data-toggle="modal"
                data-target="#composeForm"><i class="feather icon-edit"></i> Compose</button>
      </div>      <div class="sidebar-menu-list">
        <div class="list-group list-group-messages font-medium-1">
          <a href="{{ route('emails') }}" class="list-group-item list-group-item-action border-0 pt-0 @if(Route::currentRouteName() == 'emails') active @endif">
            <i class="font-medium-5 feather icon-mail mr-50"></i> Inbox
            @if(Route::currentRouteName() == 'emails')
                (All)
            @elseif(Route::currentRouteName() == 'emails.mrd')
              (MRD)
            @elseif(Route::currentRouteName() == 'emails.accounts')
              (Accounts)
            @elseif(Route::currentRouteName() == 'emails.noreply')
              (NoReply)
            @elseif(Route::currentRouteName() == 'emails.bookings')
              (Booking)
            @endif

{{--            <span class="badge badge-primary badge-pill float-right">3</span>--}}
          </a>
          <a href="{{ route('emails.sent') }}" class="list-group-item list-group-item-action border-0 @if(Route::currentRouteName() == 'emails.sent') active @endif"><i
              class="font-medium-5 fa fa-paper-plane-o mr-50"></i> Sent</a>
          <a href="javascript:void(0)" class="list-group-item list-group-item-action border-0" data-toggle="modal" data-target="#searchMail"><i
              class="font-medium-5 fa fa-search mr-50"></i> Search</a>
{{--          <a href="#" class="list-group-item list-group-item-action border-0"><i--}}
{{--              class="font-medium-5 feather icon-edit-2 mr-50"></i> Draft <span--}}
{{--              class="badge badge-warning badge-pill float-right">4</span> </a>--}}
{{--          <a href="#" class="list-group-item list-group-item-action border-0"><i class="font-medium-5 feather icon-star mr-50"></i>--}}
{{--            Starred</a>--}}
{{--          <a href="#" class="list-group-item list-group-item-action border-0"><i class="font-medium-5 feather icon-info mr-50"></i>--}}
{{--            Spam <span class="badge badge-danger badge-pill float-right">3</span> </a>--}}
{{--          <a href="#" class="list-group-item list-group-item-action border-0"><i class="font-medium-5 feather icon-trash mr-50"></i>--}}
{{--            Trash</a>--}}
        </div>
        <hr>
        <h5 class="my-2 pt-25">Accounts</h5>
        <div class="list-group list-group-labels font-medium-1">
          <a href="{{ route('emails.mrd') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center"><span
              class="bullet bullet-success mr-1"></span> MRD</a>
          <a href="{{ route('emails.accounts') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center"><span
              class="bullet bullet-primary mr-1"></span> Accounts</a>
          <a href="{{ route('emails.bookings') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center"><span
              class="bullet bullet-warning mr-1"></span> Bookings</a>
          <a href="{{ route('emails.noreply') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center"><span
              class="bullet bullet-danger mr-1"></span>No Reply</a>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal -->
  <div class="modal fade text-left" id="composeForm" tabindex="-1" role="dialog" aria-labelledby="emailCompose"
       aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title text-text-bold-600" id="emailCompose">New Message</h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body pt-1">
          <div class="form-label-group mt-1">
            <input type="text" id="emailTo" class="form-control" placeholder="To" name="fname-floating">
            <label for="emailTo">To</label>
          </div>
          <div class="form-label-group">
            <input type="text" id="emailSubject" class="form-control" placeholder="Subject" name="fname-floating">
            <label for="emailSubject">Subject</label>
          </div>
          <div class="form-label-group">
            <input type="text" id="emailCC" class="form-control" placeholder="CC" name="fname-floating">
            <label for="emailCC">CC</label>
          </div>
          <div class="form-label-group">
            <input type="text" id="emailBCC" class="form-control" placeholder="BCC" name="fname-floating">
            <label for="emailBCC">BCC</label>
          </div>
            <br>
          <div class="form-label-group">
            <input type="text" id="emailBCC" class="form-control" placeholder="BCC" name="fname-floating">
            <select id="emailTemplate" class="form-control">
              <option value="">--SELECT--</option>
              @foreach(\App\Templates::all() as $template)
              <option value="{{ $template->id }}" onclick="alert('{{$template->id}}')">{{ $template->mail_template_name }}</option>
              @endforeach
            </select>
            <label for="emailTemplate">Template</label>
          </div>
          <div id="email-container">
            <div class="editor" data-placeholder="Message">
            </div>
          </div>
          <div class="form-group mt-2">
            <div class="custom-file">
              <input type="file" class="custom-file-input" id="emailAttach">
              <label class="custom-file-label" for="emailAttach">Attach file</label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <input type="submit" value="Send" class="btn btn-primary">
          <input type="Reset" value="Cancel" class="btn btn-white" data-dismiss="modal">
        </div>
      </div>
    </div>
  </div>
@endsection

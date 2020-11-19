
@extends('layouts/contentLayoutMaster')

@section('title', 'Emails')

@section('vendor-style')
  <!-- vendor css files -->
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/katex.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/monokai-sublime.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.snow.css')) }}">
@endsection
@section('page-style')
  <!-- Page css files -->
  <link rel="stylesheet" href="{{ asset(mix('css/pages/app-email.css')) }}">
@endsection
<!-- Sidebar Area -->

@include('client.emails.emailSidebar')
@section('content')

  <div class="app-content-overlay"></div>
  <div class="email-app-area">
    <!-- Email list Area -->
    <div class="email-app-list-wrapper">
      <div class="email-app-list">
        <div class="app-fixed-search">
          <div class="sidebar-toggle d-block d-lg-none"><i class="feather icon-menu"></i></div>
          <fieldset class="form-group position-relative has-icon-left m-0">
            <input type="text" class="form-control" id="email-search" placeholder="Search email">
            <div class="form-control-position">
              <i class="feather icon-search"></i>
            </div>
          </fieldset>
        </div>
        <div class="app-action">
          <div class="action-left">
{{--            <div class="vs-checkbox-con selectAll">--}}
{{--              <input type="checkbox" >--}}
{{--              <span class="vs-checkbox">--}}
{{--                            <span class="vs-checkbox--check">--}}
{{--                              <i class="vs-icon feather icon-minus"></i>--}}
{{--                            </span>--}}
{{--                          </span>--}}
{{--              <span>Select All</span>--}}
{{--            </div>--}}
          </div>
          <div class="action-right">
            <ul class="list-inline m-0">
                <li class="list->inline-item">
                  @if(isset($search))
                  {{ $emails->links() }}
                    @endif
                </li>
                  @if(isset($search))
              <li class="list->inline-item">
{{--                  {{ dd($search) }}--}}
                @if($search['mailDate'] != null )
                  Found {{ $emails->count() }} Received on {{ $search['mailDate'] }}
                  @endif
                </li>
                    @endif
{{--              <li class="list-inline-item">--}}
{{--                <div class="dropdown">--}}
{{--                  <a href="#" class="dropdown-toggle" id="folder" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">--}}
{{--                    <i class="feather icon-folder"></i>--}}
{{--                  </a>--}}
{{--                  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="folder">--}}
{{--                    <a class="dropdown-item d-flex font-medium-1" href="#"><i class="font-medium-3 feather icon-edit-2 mr-50"></i> Draft</a>--}}
{{--                    <a class="dropdown-item d-flex font-medium-1" href="#"><i class="font-medium-3 feather icon-info mr-50"></i> Spam</a>--}}
{{--                    <a class="dropdown-item d-flex font-medium-1" href="#"><i class="font-medium-3 feather icon-trash mr-50"></i> Trash</a>--}}
{{--                  </div>--}}
{{--                </div>--}}
{{--              </li>--}}
{{--              <li class="list-inline-item">--}}
{{--                <div class="dropdown">--}}
{{--                  <a href="#" class="dropdown-toggle" id="tag" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">--}}
{{--                    <i class="feather icon-tag"></i>--}}
{{--                  </a>--}}
{{--                  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="tag">--}}
{{--                    <a href="#" class="dropdown-item font-medium-1"><span class="mr-1 bullet bullet-success bullet-sm"></span> Personal</a>--}}
{{--                    <a href="#" class="dropdown-item font-medium-1"><span class="mr-1 bullet bullet-primary bullet-sm"></span> Company</a>--}}
{{--                    <a href="#" class="dropdown-item font-medium-1"><span class="mr-1 bullet bullet-warning bullet-sm"></span> Important</a>--}}
{{--                    <a href="#" class="dropdown-item font-medium-1"><span class="mr-1 bullet bullet-danger bullet-sm"></span> Private</a>--}}
{{--                  </div>--}}
{{--                </div>--}}
{{--              </li>--}}
{{--              <li class="list-inline-item mail-unread"><span class="action-icon"><i class="feather icon-mail"></i></span></li>--}}
{{--              <li class="list-inline-item mail-delete"><span class="action-icon"><i class="feather icon-trash"></i></span></li>--}}
            </ul>
          </div>
        </div>
        <div class="email-user-list list-group">
          <ul class="users-list-wrapper media-list">
            @forelse($emails as $email)
              <li class="media @if($email->read) mail-read @endif" onclick="getMailContent('{{ $email->id }}')">
              <div class="media-left pr-50">
                <div class="avatar">
                  <img src="{{avatar(unserialize($email->sender)[0]->personal == false ?  unserialize($email->sender)[0]->mailbox : strtoupper(unserialize($email->sender)[0]->personal))}}" alt="avtar img holder">
                </div>
                <div class="user-action">
{{--                  <div class="vs-checkbox-con">--}}
{{--                    <input type="checkbox" >--}}
{{--                    <span class="vs-checkbox vs-checkbox-sm">--}}
{{--                                        <span class="vs-checkbox--check">--}}
{{--                                          <i class="vs-icon feather icon-check"></i>--}}
{{--                                        </span>--}}
{{--                                      </span>--}}
{{--                  </div>--}}
                  <span class="favorite"><i class="feather icon-star"></i></span>
                </div>
              </div>
              <div class="media-body">
                <div class="user-details">
                  <div class="mail-items">
                    <h5 class="list-group-item-heading text-bold-600 mb-25">{{  unserialize($email->sender)[0]->personal == false ?  unserialize($email->sender)[0]->mailbox : strtoupper(unserialize($email->sender)[0]->personal)  }}</h5>
                    <span class="list-group-item-text text-truncate">{{ $email->subject == '' ? '(No Subject)' : $email->subject }}</span>
                  </div>
                  <div class="mail-meta-item">
                                      <span class="float-right">
                                          @if($email->reply->count())
                                          <span class="mr-1"><i class="fa fa-envelope"></i></span>
                                        @endif
                                            @if($email->client)
                                              <a href="{{ route('view.client',['slug'=>$email->client->slug]) }}"><span class="mr-1"><i class="fa fa-user-circle"></i></span></a>
                                        @endif
                                        <strong>{{ strtoupper($email->account) }}</strong>
                                        <span class="mail-date">{{ \Carbon\Carbon::parse($email->date)->format('F d,Y') }}</span>
                                      </span>
                  </div>
                </div>
                <div class="mail-message">
                  <p class="list-group-item-text truncate mb-0">{{ $email->text_body }}</p>
                </div>
              </div>
            </li>
            @empty

            @endforelse
          </ul>
          <div class="no-results">
            <h5>No Items Found</h5>
          </div>
        </div>
      </div>
    </div>
    <!--/ Email list Area -->
    <!-- Detailed Email View -->
    <div class="email-app-details">
      <div class="email-detail-header">
        <div class="email-header-left d-flex align-items-center mb-1">
          <span class="go-back mr-1"><i class="feather icon-arrow-left font-medium-4"></i></span>
          <h3 class="mailSubject"></h3>
        </div>
        <div class="email-header-right mb-1 ml-2 pl-1">
          <ul class="list-inline m-0">
            <li class="list-inline-item emailClient">

            </li>
{{--            <li class="list-inline-item">--}}
{{--              <div class="dropdown no-arrow">--}}
{{--                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">--}}
{{--                  <i class="feather icon-folder font-medium-5"></i>--}}
{{--                </a>--}}
{{--                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="folder">--}}
{{--                  <a class="dropdown-item d-flex font-medium-1" href="#"><i class="font-medium-3 feather icon-edit-2 mr-50"></i> Draft</a>--}}
{{--                  <a class="dropdown-item d-flex font-medium-1" href="#"><i class="font-medium-3 feather icon-info mr-50"></i> Spam</a>--}}
{{--                  <a class="dropdown-item d-flex font-medium-1" href="#"><i class="font-medium-3 feather icon-trash mr-50"></i> Trash</a>--}}
{{--                </div>--}}
{{--              </div>--}}
{{--            </li>--}}
{{--            <li class="list-inline-item">--}}
{{--              <div class="dropdown no-arrow">--}}
{{--                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">--}}
{{--                  <i class="feather icon-tag font-medium-5"></i>--}}
{{--                </a>--}}
{{--                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="tag">--}}
{{--                  <a href="#" class="dropdown-item font-medium-1"><span class="mr-1 bullet bullet-success bullet-sm"></span> Personal</a>--}}
{{--                  <a href="#" class="dropdown-item font-medium-1"><span class="mr-1 bullet bullet-primary bullet-sm"></span> Company</a>--}}
{{--                  <a href="#" class="dropdown-item font-medium-1"><span class="mr-1 bullet bullet-warning bullet-sm"></span> Important</a>--}}
{{--                  <a href="#" class="dropdown-item font-medium-1"><span class="mr-1 bullet bullet-danger bullet-sm"></span> Private</a>--}}
{{--                </div>--}}
{{--              </div>--}}
{{--            </li>--}}
{{--            <li class="list-inline-item"><span class="action-icon"><i class="feather icon-mail font-medium-5"></i></span></li>--}}
{{--            <li class="list-inline-item"><span class="action-icon"><i class="feather icon-trash font-medium-5"></i></span></li>--}}
{{--            <li class="list-inline-item email-prev"><span class="action-icon"><i class="feather icon-chevrons-left font-medium-5"></i></span></li>--}}
{{--            <li class="list-inline-item email-next"><span class="action-icon"><i class="feather icon-chevrons-right font-medium-5"></i></span></li>--}}
          </ul>
        </div>
      </div>
      <div class="email-scroll-area">
        <div class="row">
          <div class="col-12">
            <div class="email-label ml-2 my-2 pl-1">
              <span class="mr-1 bullet bullet-primary bullet-sm"></span><small class="mail-label"></small>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="card px-1">
              <div class="card-header email-detail-head ml-75">
                <div class="user-details d-flex justify-content-between align-items-center flex-wrap">
                  <div class="avatar mr-75">
                    <img src="" alt="avtar img holder" width="61" height="61">
                  </div>
                  <div class="mail-items">
                    <h4 class="list-group-item-heading mb-0"><span class="mailName"></span></h4>
                    <div class="email-info-dropup dropdown">
                                          <span class="dropdown-toggle font-small-3" id="dropdownMenuButton200" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="mailFrom"></span>
                                          </span>
                      <div class="dropdown-menu dropdown-menu-right p-50" aria-labelledby="dropdownMenuButton200">
                        <div class="px-25 dropdown-item">From: <strong> <span class="mailFrom"></span> </strong></div>
                        <div class="px-25 dropdown-item">To: <strong> <span class="mailTo"></span> </strong></div>
                        <div class="px-25 dropdown-item">Date: <strong><span class="mailDateTime"></span></strong></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="mail-meta-item">
                  <div class="mail-time mb-1"><span class="mailTime"></span></div>
                  <div class="mail-date"><span class="mailDate"></span></div>
                </div>
              </div>
              <div class="card-body mail-message-wrapper pt-2 mb-0">
                <div class="mail-message">
                 </div>
{{--                <div class="mail-attachements d-flex">--}}
{{--                  <i class="feather icon-paperclip font-medium-5 mr-50"></i>--}}
{{--                  <span>Attachments</span>--}}
{{--                </div>--}}
              </div>
{{--              <div class="mail-files py-2">--}}
{{--                <div class="chip chip-primary">--}}
{{--                  <div class="chip-body py-50">--}}
{{--                    <span class="chip-text">interdum.docx</span>--}}
{{--                  </div>--}}
{{--                </div>--}}
{{--              </div>--}}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <span class="font-medium-1">Click here to <span class="primary cursor-pointer"><strong>Reply</strong></span> or <span class="primary  cursor-pointer"><strong>Forward</strong></span></span>
                  <i class="feather icon-paperclip font-medium-5 mr-50"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--/ Detailed Email View -->
  </div>
@endsection

@section('vendor-script')
  <!-- vendor js files -->
  <script src="{{ asset(mix('vendors/js/editors/quill/katex.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/editors/quill/highlight.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/editors/quill/quill.min.js')) }}"></script>
@endsection
@section('page-script')
  <!-- Page js files -->
{{--  <script src="{{ asset(mix('js/scripts/pages/app-email.js')) }}"></script>--}}
  <script>
    //  Notifications & messages scrollable
    function getMailContent(id) {
      // console.log(id);
      axios.get('/email/'+id)
      .then((response)=>{
        $('.mailName').html(response.data.name)
        $('.mailFrom').html(response.data.from)
        $('.mailTo').html(response.data.to)
        $('.mailDate').html(response.data.date)
        $('.mailTime').html(response.data.time)
        $('.mail-message').html(response.data.body)
        $('.avatar').html(response.data.avatar)
        $('.mailSubject').html(response.data.subject)
        $('.emailClient').html(response.data.client)
      });
    }
    $(function () {
      "use strict";

      var Font = Quill.import('formats/font');
      Font.whitelist = ['sofia', 'slabo', 'roboto', 'inconsolata', 'ubuntu'];
      Quill.register(Font, true);

      // if it is not touch device
      if (!$.app.menu.is_touch_device()) {
        // Email left Sidebar
        if ($('.sidebar-menu-list').length > 0) {
          var sidebar_menu_list = new PerfectScrollbar(".sidebar-menu-list");
        }

        // User list scroll
        if ($('.email-user-list').length > 0) {
          var users_list = new PerfectScrollbar(".email-user-list");
        }

        // Email detail section
        if ($('.email-scroll-area').length > 0) {
          var users_list = new PerfectScrollbar(".email-scroll-area");
        }
        // Modal dialog scroll
        if ($('.modal-dialog-scrollable .modal-body').length > 0) {
          var sidebar_menu_list = new PerfectScrollbar(".modal-dialog-scrollable .modal-body");
        }
      }

      // if it is a touch device
      else {
        $(".sidebar-menu-list").css("overflow", "scroll");
        $(".email-user-list").css("overflow", "scroll");
        $(".email-scroll-area").css("overflow", "scroll");
        $(".modal-dialog-scrollable .modal-body").css("overflow", "scroll");
      }

      // Compose Modal - Reset Input Value on Click compose btn
      $('.compose-btn .btn').on('click', function (e) {
        // all input forms
        $(".modal .modal-body input").val("");
        // quill editor content
        var quill_editor = $(".modal .modal-body .ql-editor");
        quill_editor[0].innerHTML = "";
        // file input content
        var file_input = $(".modal .modal-body .custom-file .custom-file-label");
        file_input[0].innerHTML = "";
      });

      // Main menu toggle should hide app menu
      $('.menu-toggle').on('click', function (e) {
        $('.app-content .sidebar-left').removeClass('show');
        $('.app-content .app-content-overlay').removeClass('show');
      });

      // On sidebar close click
      $(".email-application .sidebar-close-icon").on('click', function () {
        $('.sidebar-left').removeClass('show');
        $('.app-content-overlay').removeClass('show');
      });

      // Email sidebar toggle
      $('.sidebar-toggle').on('click', function (e) {
        e.stopPropagation();
        $('.app-content .sidebar-left').toggleClass('show');
        $('.app-content .app-content-overlay').addClass('show');
      });
      $('.app-content .app-content-overlay').on('click', function (e) {
        $('.app-content .sidebar-left').removeClass('show');
        $('.app-content .app-content-overlay').removeClass('show');
      });

      // Email Right sidebar toggle
      $('.email-app-list .email-user-list li').on('click', function (e) {
        // console.log(this);

        $('.app-content .email-app-details').toggleClass('show');
      });

      // Add class active on click of sidebar list
      $(".email-application .list-group-messages a").on('click', function () {
        if ($('.email-application .list-group-messages a').hasClass('active')) {
          $('.email-application .list-group-messages a').removeClass('active');
        }
        $(this).addClass("active");
      });

      // Email detail view back button click
      $('.go-back').on('click', function (e) {
        location.reload()
        e.stopPropagation();
        $('.app-content .email-app-details').removeClass('show');
      });

      // For app sidebar on small screen
      if ($(window).width() > 768) {
        if ($('.app-content .app-content-overlay').hasClass('show')) {
          $('.app-content .app-content-overlay').removeClass('show');
        }
      }
      // Favorite star click
      $(".email-application .favorite i").on("click", function (e) {
        $(this).parent('.favorite').toggleClass("warning");
        e.stopPropagation();
      });

      // On checkbox click stop propogation
      $(".email-user-list .vs-checkbox-con input").on("click", function (e) {
        e.stopPropagation();
      });

      // Select all checkbox
      $(document).on("click", ".email-app-list .selectAll input", function () {
        $(".user-action .vs-checkbox-con input").prop('checked', this.checked);
      });

      // Delete Mail from list
      $(".email-application .mail-delete").on("click", function () {
        $(".email-application .user-action .vs-checkbox-con input:checked").closest("li").remove();
        $(".email-application .selectAll input").prop('checked', "");
      });

      // Mark mail unread
      $(".email-application .mail-unread").on("click", function () {
        $(".email-application .user-action .vs-checkbox-con input:checked").closest("li").removeClass("mail-read");
      });

      // Filter
      $(".email-app-list #email-search").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        if (value != "") {
          $(".email-user-list .users-list-wrapper li").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
          });
          var tbl_row = $(".email-user-list .users-list-wrapper li:visible").length; //here tbl_test is table name

          //Check if table has row or not
          if (tbl_row == 0) {
            $('.email-user-list .no-results').addClass('show');
          }
          else {
            if ($('.email-user-list .no-results').hasClass('show')) {
              $('.email-user-list .no-results').removeClass('show');
            }
          }
        }
        else {
          // If filter box is empty
          $(".email-user-list .users-list-wrapper li").show();
          if ($('.email-user-list .no-results').hasClass('show')) {
            $('.email-user-list .no-results').removeClass('show');
          }
        }
      });

      // Email compose Editor

      var emailEditor = new Quill('#email-container .editor', {
        bounds: '#email-container .editor',
        modules: {
          'formula': true,
          'syntax': true,
          'toolbar': [
            ['bold', 'italic', 'underline', 'strike', 'link', 'blockquote', 'code-block',
              {
                'header': '1'
              }, {
              'header': '2'
            }, {
              'list': 'ordered'
            }, {
              'list': 'bullet'
            }],
            [{
              'font': []
            }]
          ],
        },
        placeholder: 'Message',
        theme: 'snow'
      });

      var editors = [emailEditor];

    });

    $(window).on("resize", function () {
      // remove show classes from sidebar and overlay if size is > 992
      if ($(window).width() > 768) {
        if ($('.app-content .app-content-overlay').hasClass('show')) {
          $('.app-content .sidebar-left').removeClass('show');
          $('.app-content .app-content-overlay').removeClass('show');
        }
      }
    });

  </script>


@endsection


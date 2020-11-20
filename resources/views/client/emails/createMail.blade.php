
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
  <div class="row">
    <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        Compose Mail
      </div>
      <div class="card-body">
        <div class="row">
        <div class="col-md-4">
          <input type="text" id="emailTo" class="form-control" placeholder="To" name="fname-floating">
        </div>
        <div class="col-md-4">
          <input type="text" id="emailSubject" class="form-control" placeholder="Subject" name="fname-floating">
        </div>
        <div class="col-md-4">
          <input type="text" id="emailCC" class="form-control" placeholder="CC" name="fname-floating">
        </div>
        <div class="col-md-4">
          <input type="text" id="emailBCC" class="form-control" placeholder="BCC" name="fname-floating">
        </div>
        <div class="col-md-4">
          <select id="emailTemplate" class="form-control">
            <option value="">--SELECT--</option>
            @foreach(\App\Templates::all() as $template)
              <option value="{{ $template->id }}" onclick="alert('{{$template->id}}')">{{ $template->mail_template_name }}</option>
            @endforeach
          </select>
        </div>
          <div class="col-md-12">
            <br>
            <div id="email-container">
          <div class="editor" data-placeholder="Message">
          </div>
        </div>
          </div>
        <div class="div.col-md-12">
          <hr>
          <div class="custom-file">
            <input type="file" class="custom-file-input" id="emailAttach">
            <label class="custom-file-label" for="emailAttach">Attach file</label>
          </div>
        </div>
        </div>
      </div>
      <div class="card-footer text-right">
        <a href="{{ route('emails') }}">
          <button class="btn btn-secondary">Go Back</button>
        </a>
        <button class="btn btn-primary">Send</button>
      </div>
    </div>
    </div>

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

    $(function () {
      "use strict";
      const fontSizeArr = ['8px','9px','10px','12px','14px','16px','20px','24px','32px','42px','54px','68px','84px','98px'];

      var Size = Quill.import('attributors/style/size');
      Size.whitelist = fontSizeArr;
      Quill.register(Size, true);

      var Font = Quill.import('formats/font');
      Font.whitelist = ['sofia', 'slabo', 'roboto', 'inconsolata', 'ubuntu'];
      Quill.register(Font, true);

      var quill_editor = $(".editor");
      quill_editor[0].innerHTML = "";


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
            }],
            [{ 'size': fontSizeArr }]
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


    $( "#emailTemplate" ).change(function() {
      var data = $('#emailTemplate').val();
      axios.get('/email/templates/view/'+data)
        .then((response)=>{
          // console.log(response.data);
          $('.ql-editor').html(response.data.mail_template);
          $('#emailSubject').html(response.data.mail_subject);

        });
    });
  </script>


@endsection


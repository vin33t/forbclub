@extends('layouts/contentLayoutMaster')

@section('title', 'Email Templates')


@section('vendor-style')
  <!-- vendor css files -->
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/katex.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/monokai-sublime.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.snow.css')) }}">
@endsection
@section('page-style')
  <!-- Page css files -->
  <link rel="stylesheet" href="{{ asset(mix('css/pages/app-email.css')) }}">
@endsection
@section('content')

  <!-- Zero configuration table -->
  <form action="{{ route('email.templates.create') }}" method="POST" id="createNewTemplate">
    @csrf
    <input type="hidden" name="id" value="{{ $template->id }}">
    <div class="modal-body">
      <div class="row">
        <div class="col-md-12">
          <label for="templateSubject">Template Subject</label>
          <input type="text" name="templateSubject" class="form-control" required value="{{ $template->mail_subject }}">
        </div>
        <div class="col-md-12">
          <hr>
          <textarea name="templateContent" id="templateContent" style="display: none"></textarea>
          <div id="email-container">
            <div class="editor" data-placeholder="Message">
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      <button type="submit" class="btn btn-primary">Add</button>
    </div>
  </form>


  @include('client.emails.templates.addNew')
@endsection


@section('vendor-script')
  {{-- vendor files --}}
  <script src="{{ asset(mix('vendors/js/tables/datatable/pdfmake.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/vfs_fonts.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.html5.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.print.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>

  <script src="{{ asset(mix('vendors/js/editors/quill/katex.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/editors/quill/highlight.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/editors/quill/quill.min.js')) }}"></script>

@endsection
@section('page-script')
  {{-- Page js files --}}
  <script src="{{ asset(mix('js/scripts/datatables/datatable.js')) }}"></script>

  <script>
    $(function () {
      "use strict";

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
            }]
          ],
        },
        placeholder: 'Message',
        theme: 'snow'
      });

      // var editors = [emailEditor];

    });
  </script>
  <script>
    function viewTemplate(id){
      $('.activeTemplateName').html('')
      $('.activeTemplateSubject').html('')
      $('.activeTemplateContent').html('')
      $('#viewTemplate').modal()
      axios.get('/email/templates/view/'+id)
        .then((response)=>{
          $('.activeTemplateName').html(response.data.mail_template_name)
          $('.activeTemplateSubject').html(response.data.mail_subject)
          $('.activeTemplateContent').html(response.data.mail_template)
        })
    }
    $('#createNewTemplate').submit(function() {
      // var html = editor.root.innerHTML;
      $('#templateContent').html($('.ql-editor').html());
      // return false;
    });
  </script>
@endsection







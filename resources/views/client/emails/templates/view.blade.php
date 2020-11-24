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
  <section id="basic-datatable">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">{{ $templates->count() }} Mail Templates <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addNewTemplate"><i class="fa fa-plus"></i> Add New</button></h4>
          </div>
          <div class="card-content">
            <div class="card-body card-dashboard">
              <div class="table-responsive">
                <table class="table zero-configuration">
                  <thead>
                  <tr>
                    <th>#</th>
                    <th>Subject</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($templates as $template)
                    <tr>
                      <td  onclick="viewTemplate('{{ $template->id }}')">{{ $loop->index + 1 }}</td>
                      <td  onclick="viewTemplate('{{ $template->id }}')">{{ $template->mail_subject }}</td>
                      <td>
                        <button class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></button>
                        <a href="{{ route('email.templates.delete',['id'=>$template->id]) }}">
                          <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                        </a>
                      </td>
                    </tr>
                  @endforeach
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>#</th>
                    <th>Subject</th>
                    <th>Action</th>
                  </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <div class="modal fade" id="viewTemplate" tabindex="-1" role="dialog" aria-labelledby="viewTemplate" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Template</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              Name: <span class="activeTemplateName"></span>
            </div>
            <div class="col-md-12">
              Subject: <span class="activeTemplateSubject"></span>
            </div>
            <div class="col-md-12">
              <span class="activeTemplateContent"></span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

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







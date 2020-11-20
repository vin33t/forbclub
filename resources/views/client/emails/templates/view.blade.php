@extends('layouts/contentLayoutMaster')

@section('title', 'Email Templates')


@section('vendor-style')
  <!-- vendor css files -->
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/katex.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/monokai-sublime.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.snow.css')) }}">
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
                    <th>Template Name</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($templates as $template)
                    <tr>
                      <td>{{ $loop->index + 1 }}</td>
                      <td>{{ $template->mail_subject }}</td>
                      <td>{{ $template->mail_template_name }}</td>
                    </tr>
                  @endforeach
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>#</th>
                    <th>Subject</th>
                    <th>Template Name</th>
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

      var editors = [emailEditor];

    });
  </script>
@endsection

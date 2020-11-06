{{-- Vendor Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="{{ asset(mix('vendors/js/vendors.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/ui/prism.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>

@yield('vendor-script')
{{-- Theme Scripts --}}
<script src="{{ asset(mix('js/core/app-menu.js')) }}"></script>
<script src="{{ asset(mix('js/core/app.js')) }}"></script>
{{--<script src="{{ asset(mix('js/app.js')) }}"></script>--}}

<script src="{{ asset(mix('js/scripts/components.js')) }}"></script>
@if($configData['blankPage'] == false)
<script src="{{ asset(mix('js/scripts/customizer.js')) }}"></script>
<script src="{{ asset(mix('js/scripts/footer.js')) }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js" ></script>

@endif
{{-- page script --}}
@yield('page-script')
<script>
  function notificationToast(type,title,message){
    if(type === 'success'){

      toastr.success(message,title, { "progressBar": true , positionClass: 'toast-top-center', containerId: 'toast-top-center'});
    } else if(type === 'info'){
      toastr.info(message, title, { "progressBar": true , positionClass: 'toast-top-center', containerId: 'toast-top-center'});
    } else if(type === 'warning'){
      toastr.warning(message, title, { "progressBar": true , positionClass: 'toast-top-center', containerId: 'toast-top-center'});
    } else if(type === 'error'){
      toastr.error(message, title, { "progressBar": true , positionClass: 'toast-top-center', containerId: 'toast-top-center'});
    }
  }
  @if(session('notifyToast'))
  notificationToast('{{session('notifyType')}}','{{session('notifyTitle')}}','{{session('notifyMessage')}}')
  @endif
</script>
<script>
  jQuery(document).ready(function($) {

    // Set the Options for "Bloodhound" suggestion engine
    var engine = new Bloodhound({
      remote: {
        url: '/find?q=%QUERY%',
        wildcard: '%QUERY%'
      },
      datumTokenizer: Bloodhound.tokenizers.whitespace('q'),
      queryTokenizer: Bloodhound.tokenizers.whitespace
    });
    $(".search-input").typeahead({
      hint: true,
      highlight: true,
      autocomplete: true,
      minLength: 2,
      valueKey: 'name'
    }, {
      source: engine.ttAdapter(),

      // This will be appended to "tt-dataset-" to form the class name of the suggestion menu.
      name: 'clientList',

      // the key from the array we want to display (name,id,email,etc...)
      templates: {
        empty: [
          '<div class="list-group search-results-dropdown"><div class="list-group-item">No Client found.</div></div>'
        ],
        header: [
          '<div class="list-group search-results-dropdown">'
        ],
        suggestion: function (data) {
          return '<a href="/booking/create/' +  data.slug + '" class="list-group-item" onclick="block()">' + data.name + ' - @' + data.phone + ' - ' + data.email +'</a>'
        }
      }
    });
  });
</script>

{{ forgetNotifyToast() }}
{{-- page script --}}

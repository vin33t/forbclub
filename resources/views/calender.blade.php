@extends('layouts/contentLayoutMaster')

@section('title', 'Calender')

@section('vendor-style')
  <!-- Vendor css files -->
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/calendars/fullcalendar.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/calendars/extensions/daygrid.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/calendars/extensions/timegrid.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/pickadate/pickadate.css')) }}">
@endsection
@section('page-style')
  <!-- Page css files -->
  <link rel="stylesheet" href="{{ asset(mix('css/plugins/calendars/fullcalendar.css')) }}">
@endsection
@section('content')
  <!-- Full calendar start -->
  <section id="basic-examples">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-content">
            <div class="card-body">
              <div class="cal-category-bullets d-none">
                <div class="bullets-group-1 mt-2">
                  <div class="category-business mr-1">
                    <span class="bullet bullet-success bullet-sm mr-25"></span>
                    Client
                  </div>
                  <div class="category-work mr-1">
                    <span class="bullet bullet-warning bullet-sm mr-25"></span>
                    Work
                  </div>
                  <div class="category-personal mr-1">
                    <span class="bullet bullet-danger bullet-sm mr-25"></span>
                    Personal
                  </div>
                  <div class="category-others">
                    <span class="bullet bullet-primary bullet-sm mr-25"></span>
                    Others
                  </div>
                </div>
              </div>
              <div id='fc-default'></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- calendar Modal starts-->
    <div class="modal fade text-left modal-calendar" tabindex="-1" role="dialog" aria-labelledby="cal-modal"
         aria-modal="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title text-text-bold-600" id="cal-modal">Add Event</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <form action="#">
            <div class="modal-body">
              <div class="d-flex justify-content-between align-items-center add-category">
                <div class="chip-wrapper"></div>
                <div class="label-icon pt-1 pb-2 dropdown calendar-dropdown">
                  <i class="feather icon-tag dropdown-toggle" id="cal-event-category" data-toggle="dropdown"></i>
                  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="cal-event-category">
                  <span class="dropdown-item business" data-color="success">
                    <span class="bullet bullet-success bullet-sm mr-25"></span>
                    Client
                  </span>
                    <span class="dropdown-item work" data-color="warning">
                    <span class="bullet bullet-warning bullet-sm mr-25"></span>
                    Work
                  </span>
                    <span class="dropdown-item personal" data-color="danger">
                    <span class="bullet bullet-danger bullet-sm mr-25"></span>
                    Personal
                  </span>
                    <span class="dropdown-item others" data-color="primary">
                    <span class="bullet bullet-primary bullet-sm mr-25"></span>
                    Others
                  </span>
                  </div>
                </div>
              </div>
              <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
              <fieldset class="form-label-group">
                <input type="hidden" class="form-control" id="cal-id">
                <input type="text" class="form-control" id="cal-event-title" placeholder="Event Title">
                <label for="cal-event-title">Event Title</label>
              </fieldset>
              <fieldset class="form-label-group">
                <input type="text" class="form-control pickadate" id="cal-start-date" placeholder="Start Date">
                <label for="cal-start-date">Start Date</label>
              </fieldset>
              <fieldset class="form-label-group">
                <input type="text" class="form-control pickadate" id="cal-end-date" placeholder="End Date">
                <label for="cal-end-date">End Date</label>
              </fieldset>
              <fieldset class="form-label-group">
                <textarea class="form-control" id="cal-description" rows="5" placeholder="Description"></textarea>
                <label for="cal-description">Description</label>
              </fieldset>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary cal-add-event waves-effect waves-light" disabled>
                Add Event</button>
              <button type="button" class="btn btn-primary d-none cal-submit-event waves-effect waves-light"
                      disabled>Update</button>
              <button type="button" class="btn btn-flat-danger cancel-event waves-effect waves-light"
                      data-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-flat-danger remove-event d-none waves-effect waves-light"
                      data-dismiss="modal">Remove</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- calendar Modal ends-->
  </section>
  <!-- // Full calendar end -->
@endsection

@section('vendor-script')
  <!-- Vendor js files -->
  <script src="{{ asset(mix('vendors/js/extensions/moment.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/calendar/fullcalendar.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/calendar/extensions/daygrid.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/calendar/extensions/timegrid.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/calendar/extensions/interactions.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>
@endsection
@section('page-script')
  <!-- Page js files -->
{{--  <script src="{{ asset(mix('js/scripts/extensions/fullcalendar.js')) }}"></script>--}}
  <script>

    document.addEventListener('DOMContentLoaded', function () {

      // color object for different event types
      var colors = {
        primary: "#7367f0",
        success: "#28c76f",
        danger: "#ea5455",
        warning: "#ff9f43"
      };

      // chip text object for different event types
      var categoryText = {
        primary: "Others",
        success: "Client",
        danger: "Personal",
        warning: "Work"
      };
      var categoryBullets = $(".cal-category-bullets").html(),
        evtColor = "",
        eventColor = "";

      // calendar init
      var calendarEl = document.getElementById('fc-default');

      var calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: ["dayGrid", "timeGrid", "interaction"],
        customButtons: {
          addNew: {
            text: ' Add',
            click: function () {
              var calDate = new Date,
                todaysDate = calDate.toISOString().slice(0, 10);
              $(".modal-calendar").modal("show");
              $(".modal-calendar .cal-submit-event").addClass("d-none");
              $(".modal-calendar .remove-event").addClass("d-none");
              $(".modal-calendar .cal-add-event").removeClass("d-none")
              $(".modal-calendar .cancel-event").removeClass("d-none")
              $(".modal-calendar .add-category .chip").remove();
              $("#cal-start-date").val(todaysDate);
              $("#cal-end-date").val(todaysDate);
              $(".modal-calendar #cal-start-date").attr("disabled", false);
            }
          }
        },
        header: {
          left: "addNew",
          center: "dayGridMonth,timeGridWeek,timeGridDay",
          right: "prev,title,next"
        },
        displayEventTime: false,
        navLinks: true,
        editable: true,
        allDay: true,
        navLinkDayClick: function (date) {
          $(".modal-calendar").modal("show");
        },
        dateClick: function (info) {
          $(".modal-calendar #cal-start-date").val(info.dateStr).attr("disabled", true);
          $(".modal-calendar #cal-end-date").val(info.dateStr);
        },
        // displays saved event values on click
        eventClick: function (info) {
          $(".modal-calendar").modal("show");
          $(".modal-calendar #cal-event-title").val(info.event.title);
          $(".modal-calendar #cal-id").val(info.event.id);
          $(".modal-calendar #cal-start-date").val(moment(info.event.start).format('YYYY-MM-DD'));
          $(".modal-calendar #cal-end-date").val(moment(info.event.end).format('YYYY-MM-DD'));
          $(".modal-calendar #cal-description").val(info.event.extendedProps.description);
          $(".modal-calendar .cal-submit-event").removeClass("d-none");
          $(".modal-calendar .remove-event").removeClass("d-none");
          $(".modal-calendar .cal-add-event").addClass("d-none");
          $(".modal-calendar .cancel-event").addClass("d-none");
          $(".calendar-dropdown .dropdown-menu").find(".selected").removeClass("selected");
          var eventCategory = info.event.extendedProps.dataEventColor;
          var eventText = categoryText[eventCategory]
          $(".modal-calendar .chip-wrapper .chip").remove();
          $(".modal-calendar .chip-wrapper").append($("<div class='chip chip-" + eventCategory + "'>" +
            "<div class='chip-body'>" +
            "<span class='chip-text'> " + eventText + " </span>" +
            "</div>" +
            "</div>"));
        },
      });

      // render calendar
      calendar.render();

      // appends bullets to left class of header
      $("#basic-examples .fc-right").append(categoryBullets);

      // Close modal on submit button
      $(".modal-calendar .cal-submit-event").on("click", function () {
        $(".modal-calendar").modal("hide");
        var eventTitle = $("#cal-event-title").val(),
          startDate = $("#cal-start-date").val(),
          endDate = $("#cal-end-date").val(),
          eventDescription = $("#cal-description").val(),
          token = $("#_token").val(),
          correctEndDate = new Date(endDate),
          id = $("#cal-id").val();
        console.log(id);
        var bodyFormData = new FormData();
        bodyFormData.append('id', id);
        bodyFormData.append('startDate', startDate);
        bodyFormData.append('endDate', endDate);
        bodyFormData.append('title', eventTitle);
        bodyFormData.append('description', eventDescription);
        bodyFormData.append('_token', token);
        bodyFormData.append('color', evtColor);
        bodyFormData.append('eventColor', evtColor);
        bodyFormData.append('allDay', true);
        // console.log(bodyFormData)
        axios({
          method: 'post',
          url: '/todo/add',
          data: bodyFormData,
          headers: {'Content-Type': 'multipart/form-data' }
        })
          .then(function (response) {
            // console.log(response)
            location.reload();
          })
          .catch(function (response) {
            //handle error
            console.log(response);
          });


      });

      // Remove Event
      $(".remove-event").on("click", function () {
        var calId = $(".modal-calendar #cal-id").val();
        var removeEvent = calendar.getEventById(calId);
        axios.get('/todo/remove/'+calId)
        removeEvent.remove();
      });


      // reset input element's value for new event
      if ($("td:not(.fc-event-container)").length > 0) {
        $(".modal-calendar").on('hidden.bs.modal', function (e) {
          $('.modal-calendar .form-control').val('');
        })
      }

      // remove disabled attr from button after entering info
      $(".modal-calendar .form-control").on("keyup", function () {
        if ($(".modal-calendar #cal-event-title").val().length >= 1) {
          $(".modal-calendar .modal-footer .btn").removeAttr("disabled");
        }
        else {
          $(".modal-calendar .modal-footer .btn").attr("disabled", true);
        }
      });

      // open add event modal on click of day
      $(document).on("click", ".fc-day", function () {
        $(".modal-calendar").modal("show");
        $(".calendar-dropdown .dropdown-menu").find(".selected").removeClass("selected");
        $(".modal-calendar .cal-submit-event").addClass("d-none");
        $(".modal-calendar .remove-event").addClass("d-none");
        $(".modal-calendar .cal-add-event").removeClass("d-none");
        $(".modal-calendar .cancel-event").removeClass("d-none");
        $(".modal-calendar .add-category .chip").remove();
        $(".modal-calendar .modal-footer .btn").attr("disabled", true);
        evtColor = colors.primary;
        eventColor = "primary";
      });

      // change chip's and event's color according to event type
      $(".calendar-dropdown .dropdown-menu .dropdown-item").on("click", function () {
        var selectedColor = $(this).data("color");
        evtColor = colors[selectedColor];
        eventTag = categoryText[selectedColor];
        eventColor = selectedColor;

        // changes event color after selecting category
        $(".cal-add-event").on("click", function () {
          calendar.addEvent({
            color: evtColor,
            dataEventColor: eventColor,
            className: eventColor
          });
        })

        $(".calendar-dropdown .dropdown-menu").find(".selected").removeClass("selected");
        $(this).addClass("selected");

        // add chip according to category
        $(".modal-calendar .chip-wrapper .chip").remove();
        $(".modal-calendar .chip-wrapper").append($("<div class='chip chip-" + selectedColor + "'>" +
          "<div class='chip-body'>" +
          "<span class='chip-text'> " + eventTag + " </span>" +
          "</div>" +
          "</div>"));
      });

      axios.get('/todo/list')
      .then((todos)=>{
        todos.data.forEach(function(todo){
          calendar.addEvent({
            id: todo.id,
            title: todo.title,
            start: todo.startDate,
            end: todo.endDate,
            description: todo.description,
            color: todo.color,
            dataEventColor: todo.color,
            allDay: true
          });
        });
      })


      // calendar add event
      $(".cal-add-event").on("click", function () {
        $(".modal-calendar").modal("hide");
        var eventTitle = $("#cal-event-title").val(),
          startDate = $("#cal-start-date").val(),
          endDate = $("#cal-end-date").val(),
          eventDescription = $("#cal-description").val(),
          token = $("#_token").val(),
          correctEndDate = new Date(endDate);
        var bodyFormData = new FormData();
        bodyFormData.append('startDate', startDate);
        bodyFormData.append('endDate', endDate);
        bodyFormData.append('title', eventTitle);
        bodyFormData.append('description', eventDescription);
        bodyFormData.append('_token', token);
        bodyFormData.append('color', evtColor);
        bodyFormData.append('eventColor', evtColor);
        bodyFormData.append('allDay', true);
        axios({
          method: 'post',
          url: '/todo/add',
          data: bodyFormData,
          headers: {'Content-Type': 'multipart/form-data' }
        })
          .then(function (response) {
            // console.log(response)
            calendar.addEvent({
              id: response.data.id,
              title: eventTitle,
              start: startDate,
              end: correctEndDate,
              description: eventDescription,
              color: evtColor,
              dataEventColor: eventColor,
              allDay: true
            });
          })
          .catch(function (response) {
            //handle error
            console.log(response);
          });



      });

      // date picker
      $(".pickadate").pickadate({
        format: 'yyyy-mm-dd'
      });
    });

  </script>

@endsection


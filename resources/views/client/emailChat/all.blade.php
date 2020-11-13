@extends('layouts.emailChat')
@section('title')
{{ $email }} - All Emails
@stop
@section('sidebar')
<div class="sidebar" id="sidebar">
        <div class="container">
                <div class="tab-content">
                    <!-- Start of Contacts -->
                    <div class="tab-pane fade" id="members">
                        <figure class="setting"><img class="avatar-xl" src="{{ asset('theme/assets/dist/img/avatars/avatar-male-1.jpg') }}" alt="avatar"></figure>
                        <span class="logo"><img alt="sdd" src="{{ asset('theme/assets/dist/img/logo.png') }}"></span>
                        <div class="search">
                            <form class="form-inline position-relative">
                                <input type="search" class="form-control" id="people" placeholder="Search for people...">
                                <button type="button" class="btn btn-link loop"><i class="ti-search"></i></button>
                            </form>
                            <button class="btn create" data-toggle="modal" data-target="#exampleModalCenter">
                                <i class="ti-user">+</i></button>
                        </div>

                    </div>
                    <!-- End of Contacts -->
                    <!-- Start of Discussions -->
                    <div id="discussions" class="tab-pane fade in active show">
                        <figure class="setting"><img class="avatar-xl" src="{{ asset('theme/assets/dist/img/avatars/avatar-male-1.jpg') }}" alt="avatar"></figure>
                        <span class="logo"><img src="dist/img/logo.png" alt=""></span>

                        <div class="discussions" id="scroller">
                            <h1></h1>
                            <div class="list-group" id="chats" role="tablist">
                                <a href="#mrd" class="filterDiscussions all unread single active" id="list-chat-list" data-toggle="list" role="tab">
                                    <img class="avatar-md" src="{{ asset('theme/assets/dist/img/avatars/mrd.png') }}" data-toggle="tooltip" data-placement="top" title="Sarah" alt="avatar">
{{--                                    <div class="status online"></div>--}}

                                    <div class="data">
                                        <h5>Member Relation Department</h5>
                                        <div class="new bg-yellow">
{{--                                            <span>@if($mrdReceived) {{ $mrdReceived->count() . ' '}} @else {{ '0' }} @endif</span>--}}
                                        </div>
                                        {{-- <span>Mon</span> --}}
                                        <p>mrd@forbclub.com</p>
                                    </div>
                                </a>
                                <a href="#chhavi" class="filterDiscussions all unread single" id="list-chat-list" data-toggle="list" role="tab">
                                    <img class="avatar-md" src="{{ asset('theme/assets/dist/img/avatars/chavi.png') }}" data-toggle="tooltip" data-placement="top" title="Sarah" alt="avatar">
{{--                                    <div class="status online"></div>--}}

                                    <div class="data">
                                        <h5>Chhavi Sharma</h5>
                                        <div class="new bg-yellow">
{{--                                            <span>@if($chaviReceivedReceived) {{ $chaviReceivedReceived->count . ' ' }} @else {{ '0' }} @endif</span>--}}
                                        </div>
                                        {{-- <span>Mon</span> --}}
                                        <p>chavi.s@forbclub.com</p>
                                    </div>
                                </a>
                                <a href="#accounts" class="filterDiscussions all unread single" id="list-chat-list" data-toggle="list" role="tab">
                                    <img class="avatar-md" src="{{ asset('theme/assets/dist/img/avatars/accounts.png') }}" data-toggle="tooltip" data-placement="top" title="Sarah" alt="avatar">
{{--                                    <div class="status online"></div>--}}

                                    <div class="data">
                                        <h5>Accounts</h5>
                                        <div class="new bg-yellow">
{{--                                            <span>@if($accountsReceived) {{ $accountsReceived->count() . ' '}} @else {{ '0' }} @endif</span>--}}
                                        </div>
                                        {{-- <span>Mon</span> --}}
                                        <p>accounts@forbcorp.com</p>
                                    </div>
                                </a>
                                <a href="#booking" class="filterDiscussions all unread single" id="list-chat-list" data-toggle="list" role="tab">
                                    <img class="avatar-md" src="{{ asset('theme/assets/dist/img/avatars/booking.png') }}" data-toggle="tooltip" data-placement="top" title="Sarah" alt="avatar">
{{--                                    <div class="status online"></div>--}}

                                    <div class="data">
                                        <h5>Booking</h5>
                                        <div class="new bg-yellow">
{{--                                            <span>@if($bookingReceived) {{ $bookingReceived->count() . ' '}} @else {{ '0' }}@endif</span>--}}
                                        </div>
                                        {{-- <span>Mon</span> --}}
                                        <p>booking@forbcorp.com</p>
                                    </div>
                                </a>
                                <a href="#travel1" class="filterDiscussions all unread single" id="list-chat-list" data-toggle="list" role="tab">
                                    <img class="avatar-md" src="{{ asset('theme/assets/dist/img/avatars/travel1.png') }}" data-toggle="tooltip" data-placement="top" title="Sarah" alt="avatar">
{{--                                    <div class="status online"></div>--}}

                                    <div class="data">
                                        <h5>Travel 1</h5>
                                        <div class="new bg-yellow">
{{--                                            <span>@if($travelReceived) {{ $travelReceived->count() . ' '}} @else {{'0'}} @endif</span>--}}
                                        </div>
                                        {{-- <span>Mon</span> --}}
                                        <p>travel1@forbtours.com</p>
                                    </div>
                                </a>
                                <a href="#travel2" class="filterDiscussions all unread single" id="list-chat-list" data-toggle="list" role="tab">
                                    <img class="avatar-md" src="{{ asset('theme/assets/dist/img/avatars/travel2.png') }}" data-toggle="tooltip" data-placement="top" title="Sarah" alt="avatar">
{{--                                    <div class="status online"></div>--}}

                                    <div class="data">
                                        <h5>Travel 2</h5>
                                        <div class="new bg-yellow">
{{--                                            <span>@if($trave2Received) {{ $trave2Received->count() . ' '}} @else {{ '0' }}@endif</span>--}}
                                        </div>
                                        {{-- <span>Mon</span> --}}
                                        <p>travel2@forbtours.com</p>
                                    </div>
                                </a>


                                <a href="#nisha" class="filterDiscussions all unread single" id="list-chat-list" data-toggle="list" role="tab">
                                    <img class="avatar-md" src="{{ asset('theme/assets/dist/img/avatars/travel2.png') }}" data-toggle="tooltip" data-placement="top" title="Sarah" alt="avatar">
{{--                                    <div class="status online"></div>--}}

                                    <div class="data">
                                        <h5>Nisha</h5>
                                        <div class="new bg-yellow">
{{--                                            <span>@if($trave2Received) {{ $trave2Received->count() . ' '}} @else {{ '0' }}@endif</span>--}}
                                        </div>
                                        {{-- <span>Mon</span> --}}
                                        <p>nisha.s@forbclub.com</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- End of Discussions -->
                </div>
        </div>
    </div><!-- Sidebar -->
@endsection

@section('content')
    <div class="main bg" id="chat-dialog">
        <div class="bg-image" style="background-image: url({{ asset('theme/assets/dist/img/avatars/pattern2.jpg') }}"></div>
        <div class="tab-content" id="nav-tabContent">
            <!-- Start of Babble -->
            <div class="babble tab-pane fade active show" id="mrd" role="tabpanel" aria-labelledby="list-chat-list">
            <!-- Start of Chat -->
            <div class="chat" id="chat1">

                <div class="top">
                    <div class="container">
                        <div class="col-md-12">
                            <div class="inside">
{{--                                <div class="status online"></div>--}}
                                <div class="data">
                                    <h5><a href="#">{{ $email }}</a></h5>
{{--                                    <span>Active now</span>--}}
                                </div>
                                <button class="btn d-md-block d-none" title="Contact Support">
                                    <i class="ti-headphone-alt"> Support</i>
                                </button>
                                <button class="btn d-md-block d-none" title="Suggestion/Issue">
                                    <i class="ti-info-alt"> Suggestion/Issue</i>
                                </button>

                                <button class="btn back-to-mesg" title="Back">
                                    <i class="ti-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content" id="content">
                    <div class="container">
                        @if($mrdReceived->count() > 0)
                            <div class="col-md-12">
                                <div class="date">
                                    <hr>
                                    <span>Received</span>
                                    <hr>
                                </div>
                                @foreach($mrdReceived as $mail)
                                    <div class="message">
                                        <img class="avatar-md" src="{{ asset('theme/assets/dist/img/avatars/avatar-female-5.jpg') }}" data-toggle="tooltip" data-placement="top" title="Karen joye" alt="avatar">
                                        <div class="text-main">
                                            <div class="text-group">
                                                <div class="text">
                                                    <b>{{ $mail->getSubject() }}</b>
                                                    <p>{!! $mail->getHTMLBody(true) !!}</p>
                                                </div>
                                            </div>
                                            <span>{{ $mail->getUid().'/'.'INBOX'.'/'.$mail->getDate()->timestamp }}</span>
                                            <span>{{ Carbon\Carbon::parse($mail->getDate())->format('l\\, F jS\\, Y\\ h:i A') }} </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                            @if($mrdSent->count() > 0)
                            <div class="col-md-12">
                                <div class="date">
                                    <hr>
                                    <span>Sent</span>
                                    <hr>
                                </div>
                                @foreach($mrdSent as $mail)
                                    <div class="message me">
                                        <img class="avatar-md" src="{{ asset('theme/assets/dist/img/avatars/avatar-female-5.jpg') }}" data-toggle="tooltip" data-placement="top" title="Karen joye" alt="avatar">
                                        <div class="text-main">
                                            <div class="text-group">
                                                <div class="text">
                                                    <b>{{ $mail->getSubject() }}</b>
                                                    <p>{!! $mail->getHTMLBody(true) !!}</p>
                                                </div>
                                            </div>
                                            <span>{{ $mail->getUid().'/'.'INBOX'.'/'.$mail->getDate()->timestamp }}</span>
                                            <span>{{ Carbon\Carbon::parse($mail->getDate())->format('l\\, F jS\\, Y\\ h:i A') }} </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                <div class="container">
                    <div class="col-md-12">
                        <div class="bottom">
                            <form class="text-area">
                                <textarea class="form-control" placeholder="Start typing for reply..." rows="1"></textarea>
                                <button type="submit" class="btn send"><i class="ti-location-arrow"></i></button>
                            </form>
                            <label>
                                <input type="file">
                                <span class="btn attach"><i class="ti-clip"></i></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Chat -->
            </div>


            <div class="babble tab-pane fade" id="chhavi" role="tabpanel" aria-labelledby="list-chat-list">
                <!-- Start of Chat -->
                <div class="chat" id="chat1">

                    <div class="top">
                        <div class="container">
                            <div class="col-md-12">
                                <div class="inside">
                                    {{--                                <div class="status online"></div>--}}
                                    <div class="data">
                                        <h5><a href="#">{{ $email }}</a></h5>
                                        {{--                                    <span>Active now</span>--}}
                                    </div>
                                    <button class="btn d-md-block d-none" title="Contact Support">
                                        <i class="ti-headphone-alt"> Support</i>
                                    </button>
                                    <button class="btn d-md-block d-none" title="Suggestion/Issue">
                                        <i class="ti-info-alt"> Suggestion/Issue</i>
                                    </button>

                                    <button class="btn back-to-mesg" title="Back">
                                        <i class="ti-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content" id="content">
                        <div class="container">
                            @if($chaviReceived->count() > 0)
                                <div class="col-md-12">
                                    <div class="date">
                                        <hr>
                                        <span>Received</span>
                                        <hr>
                                    </div>
                                    @foreach($chaviReceived as $mail)
                                        <div class="message">
                                            <img class="avatar-md" src="{{ asset('theme/assets/dist/img/avatars/avatar-female-5.jpg') }}" data-toggle="tooltip" data-placement="top" title="Karen joye" alt="avatar">
                                            <div class="text-main">
                                                <div class="text-group">
                                                    <div class="text">
                                                        <b>{{ $mail->getSubject() }}</b>
                                                        <p>{!! $mail->getHTMLBody(true) !!}</p>
                                                    </div>
                                                </div>
                                                <span>{{ $mail->getUid().'/'.'INBOX'.'/'.$mail->getDate()->timestamp }}</span>
                                                <span>{{ Carbon\Carbon::parse($mail->getDate())->format('l\\, F jS\\, Y\\ h:i A') }} </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                                @if($chaviSent->count() > 0)
                                <div class="col-md-12">
                                    <div class="date">
                                        <hr>
                                        <span>Sent</span>
                                        <hr>
                                    </div>
                                    @foreach($chaviSent as $mail)
                                        <div class="message me">
                                            <img class="avatar-md" src="{{ asset('theme/assets/dist/img/avatars/avatar-female-5.jpg') }}" data-toggle="tooltip" data-placement="top" title="Karen joye" alt="avatar">
                                            <div class="text-main">
                                                <div class="text-group">
                                                    <div class="text">
                                                        <b>{{ $mail->getSubject() }}</b>
                                                        <p>{!! $mail->getHTMLBody(true) !!}</p>
                                                    </div>
                                                </div>
                                                <span>{{ $mail->getUid().'/'.'INBOX'.'/'.$mail->getDate()->timestamp }}</span>
                                                <span>{{ Carbon\Carbon::parse($mail->getDate())->format('l\\, F jS\\, Y\\ h:i A') }} </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="container">
                        <div class="col-md-12">
                            <div class="bottom">
                                <form class="text-area">
                                    <textarea class="form-control" placeholder="Start typing for reply..." rows="1"></textarea>
                                    <button type="submit" class="btn send"><i class="ti-location-arrow"></i></button>
                                </form>
                                <label>
                                    <input type="file">
                                    <span class="btn attach"><i class="ti-clip"></i></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of Chat -->
            </div>

            <div class="babble tab-pane fade" id="accounts" role="tabpanel" aria-labelledby="list-chat-list">
                <!-- Start of Chat -->
                <div class="chat" id="chat1">

                    <div class="top">
                        <div class="container">
                            <div class="col-md-12">
                                <div class="inside">
                                    {{--                                <div class="status online"></div>--}}
                                    <div class="data">
                                        <h5><a href="#">{{ $email }}</a></h5>
                                        {{--                                    <span>Active now</span>--}}
                                    </div>
                                    <button class="btn d-md-block d-none" title="Contact Support">
                                        <i class="ti-headphone-alt"> Support</i>
                                    </button>
                                    <button class="btn d-md-block d-none" title="Suggestion/Issue">
                                        <i class="ti-info-alt"> Suggestion/Issue</i>
                                    </button>

                                    <button class="btn back-to-mesg" title="Back">
                                        <i class="ti-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content" id="content">
                        <div class="container">
                            @if($accountsReceived->count() > 0)
                                <div class="col-md-12">
                                    <div class="date">
                                        <hr>
                                        <span>Received</span>
                                        <hr>
                                    </div>
                                    @foreach($accountsReceived as $mail)
                                        <div class="message">
                                            <img class="avatar-md" src="{{ asset('theme/assets/dist/img/avatars/avatar-female-5.jpg') }}" data-toggle="tooltip" data-placement="top" title="Karen joye" alt="avatar">
                                            <div class="text-main">
                                                <div class="text-group">
                                                    <div class="text">
                                                        <b>{{ $mail->getSubject() . $mail->getTextBody(true) }}</b>
                                                        <p>{!! $mail->getHTMLBody(true) !!}</p>
                                                    </div>
                                                </div>
                                                <span>{{ $mail->getUid().'/'.'INBOX'.'/'.$mail->getDate()->timestamp }}</span>
                                                <span>{{ Carbon\Carbon::parse($mail->getDate())->format('l\\, F jS\\, Y\\ h:i A') }} </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                                @if($accountsSent->count() > 0)
                                <div class="col-md-12">
                                    <div class="date">
                                        <hr>
                                        <span>Sent</span>
                                        <hr>
                                    </div>
                                    @foreach($accountsSent as $mail)
                                        <div class="message me">
                                            <img class="avatar-md" src="{{ asset('theme/assets/dist/img/avatars/avatar-female-5.jpg') }}" data-toggle="tooltip" data-placement="top" title="Karen joye" alt="avatar">
                                            <div class="text-main">
                                                <div class="text-group">
                                                    <div class="text">
                                                        <b>{{ $mail->getSubject() }}</b>
                                                        <p>{!! $mail->getHTMLBody(true) !!}</p>
                                                    </div>
                                                </div>
                                                <span>{{ $mail->getUid().'/'.'INBOX'.'/'.$mail->getDate()->timestamp }}</span>
                                                <span>{{ Carbon\Carbon::parse($mail->getDate())->format('l\\, F jS\\, Y\\ h:i A') }} </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="container">
                        <div class="col-md-12">
                            <div class="bottom">
                                <form class="text-area">
                                    <textarea class="form-control" placeholder="Start typing for reply..." rows="1"></textarea>
                                    <button type="submit" class="btn send"><i class="ti-location-arrow"></i></button>
                                </form>
                                <label>
                                    <input type="file">
                                    <span class="btn attach"><i class="ti-clip"></i></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of Chat -->
            </div>

            <div class="babble tab-pane fade" id="booking" role="tabpanel" aria-labelledby="list-chat-list">
                <!-- Start of Chat -->
                <div class="chat" id="chat1">

                    <div class="top">
                        <div class="container">
                            <div class="col-md-12">
                                <div class="inside">
                                    {{--                                <div class="status online"></div>--}}
                                    <div class="data">
                                        <h5><a href="#">{{ $email }}</a></h5>
                                        {{--                                    <span>Active now</span>--}}
                                    </div>
                                    <button class="btn d-md-block d-none" title="Contact Support">
                                        <i class="ti-headphone-alt"> Support</i>
                                    </button>
                                    <button class="btn d-md-block d-none" title="Suggestion/Issue">
                                        <i class="ti-info-alt"> Suggestion/Issue</i>
                                    </button>

                                    <button class="btn back-to-mesg" title="Back">
                                        <i class="ti-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content" id="content">
                        <div class="container">
                            @if($bookingReceived->count() > 0)
                                <div class="col-md-12">
                                    <div class="date">
                                        <hr>
                                        <span>Received</span>
                                        <hr>
                                    </div>
                                    @foreach($bookingReceived as $mail)
                                        <div class="message">
                                            <img class="avatar-md" src="{{ asset('theme/assets/dist/img/avatars/avatar-female-5.jpg') }}" data-toggle="tooltip" data-placement="top" title="Karen joye" alt="avatar">
                                            <div class="text-main">
                                                <div class="text-group">
                                                    <div class="text">
                                                        <b>{{ $mail->getSubject() }}</b>
                                                        <p>{!! $mail->getHTMLBody(true) !!}</p>
                                                    </div>
                                                </div>
                                                <span>{{ $mail->getUid().'/'.'INBOX'.'/'.$mail->getDate()->timestamp }}</span>
                                                <span>{{ Carbon\Carbon::parse($mail->getDate())->format('l\\, F jS\\, Y\\ h:i A') }} </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                                @if($bookingSent->count() > 0)
                                <div class="col-md-12">
                                    <div class="date">
                                        <hr>
                                        <span>Sent</span>
                                        <hr>
                                    </div>
                                    @foreach($bookingSent as $mail)
                                        <div class="message me">
                                            <img class="avatar-md" src="{{ asset('theme/assets/dist/img/avatars/avatar-female-5.jpg') }}" data-toggle="tooltip" data-placement="top" title="Karen joye" alt="avatar">
                                            <div class="text-main">
                                                <div class="text-group">
                                                    <div class="text">
                                                        <b>{{ $mail->getSubject() }}</b>
                                                        <p>{!! $mail->getHTMLBody(true) !!}</p>
                                                    </div>
                                                </div>
                                                <span>{{ $mail->getUid().'/'.'INBOX'.'/'.$mail->getDate()->timestamp }}</span>
                                                <span>{{ Carbon\Carbon::parse($mail->getDate())->format('l\\, F jS\\, Y\\ h:i A') }} </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="container">
                        <div class="col-md-12">
                            <div class="bottom">
                                <form class="text-area">
                                    <textarea class="form-control" placeholder="Start typing for reply..." rows="1"></textarea>
                                    <button type="submit" class="btn send"><i class="ti-location-arrow"></i></button>
                                </form>
                                <label>
                                    <input type="file">
                                    <span class="btn attach"><i class="ti-clip"></i></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of Chat -->
            </div>

            <div class="babble tab-pane fade" id="travel1" role="tabpanel" aria-labelledby="list-chat-list">
                <!-- Start of Chat -->
                <div class="chat" id="chat1">

                    <div class="top">
                        <div class="container">
                            <div class="col-md-12">
                                <div class="inside">
                                    {{--                                <div class="status online"></div>--}}
                                    <div class="data">
                                        <h5><a href="#">{{ $email }}</a></h5>
                                        {{--                                    <span>Active now</span>--}}
                                    </div>
                                    <button class="btn d-md-block d-none" title="Contact Support">
                                        <i class="ti-headphone-alt"> Support</i>
                                    </button>
                                    <button class="btn d-md-block d-none" title="Suggestion/Issue">
                                        <i class="ti-info-alt"> Suggestion/Issue</i>
                                    </button>

                                    <button class="btn back-to-mesg" title="Back">
                                        <i class="ti-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content" id="content">
                        <div class="container">
                            @if($travel1Received->count() > 0)
                                <div class="col-md-12">
                                    <div class="date">
                                        <hr>
                                        <span>Received</span>
                                        <hr>
                                    </div>
                                    @foreach($travel1Received as $mail)
                                        <div class="message">
                                            <img class="avatar-md" src="{{ asset('theme/assets/dist/img/avatars/avatar-female-5.jpg') }}" data-toggle="tooltip" data-placement="top" title="Karen joye" alt="avatar">
                                            <div class="text-main">
                                                <div class="text-group">
                                                    <div class="text">
                                                        <b>{{ $mail->getSubject() }}</b>
                                                        <p>{!! $mail->getHTMLBody(true) !!}</p>
                                                    </div>
                                                </div>
                                                <span>{{ $mail->getUid().'/'.'INBOX'.'/'.$mail->getDate()->timestamp }}</span>
                                                <span>{{ Carbon\Carbon::parse($mail->getDate())->format('l\\, F jS\\, Y\\ h:i A') }} </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                                @if($travel1Sent->count() > 0)
                                <div class="col-md-12">
                                    <div class="date">
                                        <hr>
                                        <span>Sent</span>
                                        <hr>
                                    </div>
                                    @foreach($travel1Sent as $mail)
                                        <div class="message me">
                                            <img class="avatar-md" src="{{ asset('theme/assets/dist/img/avatars/avatar-female-5.jpg') }}" data-toggle="tooltip" data-placement="top" title="Karen joye" alt="avatar">
                                            <div class="text-main">
                                                <div class="text-group">
                                                    <div class="text">
                                                        <b>{{ $mail->getSubject() }}</b>
                                                        <p>{!! $mail->getHTMLBody(true) !!}</p>
                                                    </div>
                                                </div>
                                                <span>{{ $mail->getUid().'/'.'INBOX'.'/'.$mail->getDate()->timestamp }}</span>
                                                <span>{{ Carbon\Carbon::parse($mail->getDate())->format('l\\, F jS\\, Y\\ h:i A') }} </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="container">
                        <div class="col-md-12">
                            <div class="bottom">
                                <form class="text-area">
                                    <textarea class="form-control" placeholder="Start typing for reply..." rows="1"></textarea>
                                    <button type="submit" class="btn send"><i class="ti-location-arrow"></i></button>
                                </form>
                                <label>
                                    <input type="file">
                                    <span class="btn attach"><i class="ti-clip"></i></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of Chat -->
            </div>

            <div class="babble tab-pane fade" id="travel2" role="tabpanel" aria-labelledby="list-chat-list">
                <!-- Start of Chat -->
                <div class="chat" id="chat1">

                    <div class="top">
                        <div class="container">
                            <div class="col-md-12">
                                <div class="inside">
                                    {{--                                <div class="status online"></div>--}}
                                    <div class="data">
                                        <h5><a href="#">{{ $email }}</a></h5>
                                        {{--                                    <span>Active now</span>--}}
                                    </div>
                                    <button class="btn d-md-block d-none" title="Contact Support">
                                        <i class="ti-headphone-alt"> Support</i>
                                    </button>
                                    <button class="btn d-md-block d-none" title="Suggestion/Issue">
                                        <i class="ti-info-alt"> Suggestion/Issue</i>
                                    </button>

                                    <button class="btn back-to-mesg" title="Back">
                                        <i class="ti-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content" id="content">
                        <div class="container">
                            @if($travel2Received->count() > 0)
                                <div class="col-md-12">
                                    <div class="date">
                                        <hr>
                                        <span>Received</span>
                                        <hr>
                                    </div>
                                    @foreach($travel2Received as $mail)
                                        <div class="message">
                                            <img class="avatar-md" src="{{ asset('theme/assets/dist/img/avatars/avatar-female-5.jpg') }}" data-toggle="tooltip" data-placement="top" title="Karen joye" alt="avatar">
                                            <div class="text-main">
                                                <div class="text-group">
                                                    <div class="text">
                                                        <b>{{ $mail->getSubject() }}</b>
                                                        <p>{!! $mail->getHTMLBody(true) !!}</p>
                                                    </div>
                                                </div>
                                                <span>{{ $mail->getUid().'/'.'INBOX'.'/'.$mail->getDate()->timestamp }}</span>
                                                <span>{{ Carbon\Carbon::parse($mail->getDate())->format('l\\, F jS\\, Y\\ h:i A') }} </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                                @if($travel2Sent->count() > 0)
                                    <div class="col-md-12">
                                        <div class="date">
                                            <hr>
                                            <span>Sent</span>
                                            <hr>
                                        </div>
                                        @foreach($travel2Sent as $mail)
                                            <div class="message me">
                                                <img class="avatar-md" src="{{ asset('theme/assets/dist/img/avatars/avatar-female-5.jpg') }}" data-toggle="tooltip" data-placement="top" title="Karen joye" alt="avatar">
                                                <div class="text-main">
                                                    <div class="text-group">
                                                        <div class="text">
                                                            <b>{{ $mail->getSubject() }}</b>
                                                            <p>{!! $mail->getHTMLBody(true) !!}</p>
                                                        </div>
                                                    </div>
                                                    <span>{{ $mail->getUid().'/'.'INBOX'.'/'.$mail->getDate()->timestamp }}</span>
                                                    <span>{{ Carbon\Carbon::parse($mail->getDate())->format('l\\, F jS\\, Y\\ h:i A') }} </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                        </div>
                    </div>
                    <div class="container">
                        <div class="col-md-12">
                            <div class="bottom">
                                <form class="text-area">
                                    <textarea class="form-control" placeholder="Start typing for reply..." rows="1"></textarea>
                                    <button type="submit" class="btn send"><i class="ti-location-arrow"></i></button>
                                </form>
                                <label>
                                    <input type="file">
                                    <span class="btn attach"><i class="ti-clip"></i></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of Chat -->
            </div>

            </div>

            <div class="babble tab-pane fade" id="nisha" role="tabpanel" aria-labelledby="list-chat-list">
                <!-- Start of Chat -->
                <div class="chat" id="chat1">

                    <div class="top">
                        <div class="container">
                            <div class="col-md-12">
                                <div class="inside">
                                    {{--                                <div class="status online"></div>--}}
                                    <div class="data">
                                        <h5><a href="#">{{ $email }}</a></h5>
                                        {{--                                    <span>Active now</span>--}}
                                    </div>
                                    <button class="btn d-md-block d-none" title="Contact Support">
                                        <i class="ti-headphone-alt"> Support</i>
                                    </button>
                                    <button class="btn d-md-block d-none" title="Suggestion/Issue">
                                        <i class="ti-info-alt"> Suggestion/Issue</i>
                                    </button>

                                    <button class="btn back-to-mesg" title="Back">
                                        <i class="ti-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content" id="content">
                        <div class="container">
                            @if($nishaReceived->count() > 0)
                                <div class="col-md-12">
                                    <div class="date">
                                        <hr>
                                        <span>Received</span>
                                        <hr>
                                    </div>
                                    @foreach($travel2Received as $mail)
                                        <div class="message">
                                            <img class="avatar-md" src="{{ asset('theme/assets/dist/img/avatars/avatar-female-5.jpg') }}" data-toggle="tooltip" data-placement="top" title="Karen joye" alt="avatar">
                                            <div class="text-main">
                                                <div class="text-group">
                                                    <div class="text">
                                                        <b>{{ $mail->getSubject() }}</b>
                                                        <p>{!! $mail->getHTMLBody(true) !!}</p>
                                                    </div>
                                                </div>
                                                <span>{{ $mail->getUid().'/'.'INBOX'.'/'.$mail->getDate()->timestamp }}</span>
                                                <span>{{ Carbon\Carbon::parse($mail->getDate())->format('l\\, F jS\\, Y\\ h:i A') }} </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                                @if($nishaSent->count() > 0)
                                    <div class="col-md-12">
                                        <div class="date">
                                            <hr>
                                            <span>Sent</span>
                                            <hr>
                                        </div>
                                        @foreach($travel2Sent as $mail)
                                            <div class="message me">
                                                <img class="avatar-md" src="{{ asset('theme/assets/dist/img/avatars/avatar-female-5.jpg') }}" data-toggle="tooltip" data-placement="top" title="Karen joye" alt="avatar">
                                                <div class="text-main">
                                                    <div class="text-group">
                                                        <div class="text">
                                                            <b>{{ $mail->getSubject() }}</b>
                                                            <p>{!! $mail->getHTMLBody(true) !!}</p>
                                                        </div>
                                                    </div>
                                                    <span>{{ $mail->getUid().'/'.'INBOX'.'/'.$mail->getDate()->timestamp }}</span>
                                                    <span>{{ Carbon\Carbon::parse($mail->getDate())->format('l\\, F jS\\, Y\\ h:i A') }} </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                        </div>
                    </div>
                    <div class="container">
                        <div class="col-md-12">
                            <div class="bottom">
                                <form class="text-area">
                                    <textarea class="form-control" placeholder="Start typing for reply..." rows="1"></textarea>
                                    <button type="submit" class="btn send"><i class="ti-location-arrow"></i></button>
                                </form>
                                <label>
                                    <input type="file">
                                    <span class="btn attach"><i class="ti-clip"></i></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of Chat -->
            </div>
            <!-- End of Babble -->
        </div>
@endsection
@section('modal')

@endsection




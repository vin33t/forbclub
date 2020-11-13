@extends('layouts.emailChat')
@section('title')
{{ $client->name }}'s Mail
@stop
@section('content')
                <div class="main bg" id="chat-dialog">
					<div class="bg-image" style="background-image: url({{ asset('theme/assets/dist/img/avatars/pattern2.jpg') }})"></div>
					<div class="tab-content" id="nav-tabContent">
						<!-- Start of Babble -->
                        <div class="babble tab-pane fade active show" id="list-chat" role="tabpanel" aria-labelledby="list-chat-list">
						<!-- Start of Chat -->
						<div class="chat" id="chat1">

							<div class="top">
								<div class="container">
									<div class="col-md-12">
										<div class="inside">
											{{-- <div class="status online"></div> --}}
											<div class="data">
												<h5><a href="#">{{ $client->name }}</a></h5>
												<span>{{ $client->address }}</span>
											</div>
											<button class="btn d-md-block d-none" title="Audio call">
												<i class="ti-headphone-alt"></i> {{ $client->phone }}
											</button>
										</div>
									</div>
								</div>
							</div>
							<div class="content" id="content">
								<div class="container">
									<div class="col-md-12">
                                        @if($emails->count()>0)
										<div class="date">
											<hr>
											<span>Yesterday</span>
											<hr>
                                        </div>
                                        @foreach($emails as $mail)
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
                                        @else
                                        <div class="date">
                                                <hr>
                                                <span>No Emails form/to {{ $client->name }}</span>
                                                <hr>
                                            </div>
                                        @endif
									</div>
                                    <div class="col-md-12">
                                        @if($emails->count()>0)
                                            <div class="date">
                                                <hr>
                                                <span>Yesterday</span>
                                                <hr>
                                            </div>
                                            @foreach($sent as $mail)
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
                                        @else
                                            <div class="date">
                                                <hr>
                                                <span>No Emails form/to {{ $client->name }}</span>
                                                <hr>
                                            </div>
                                        @endif
                                    </div>
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
                </div>
@endsection

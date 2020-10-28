@if($configData["mainLayoutType"] == 'horizontal' && isset($configData["mainLayoutType"]))
  <nav class="header-navbar navbar-expand-lg navbar navbar-with-menu {{ $configData['navbarColor'] }} navbar-fixed">
    <div class="navbar-header d-xl-block d-none">
      <ul class="nav navbar-nav flex-row">
        <li class="nav-item"><a class="navbar-brand" href="dashboard-analytics">
            <div class="brand-logo"></div>
          </a></li>
      </ul>
    </div>
    @else
      <nav
        class="header-navbar navbar-expand-lg navbar navbar-with-menu {{ $configData['navbarClass'] }} navbar-light navbar-shadow {{ $configData['navbarColor'] }}">
        @endif
        <div class="navbar-wrapper">
          <div class="navbar-container content">
            <div class="navbar-collapse" id="navbar-mobile">
              <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                <ul class="nav navbar-nav">
                  <li class="nav-item mobile-menu d-xl-none mr-auto"><a
                      class="nav-link nav-menu-main menu-toggle hidden-xs"
                      href="#"><i class="ficon feather icon-menu"></i></a></li>
                </ul>

                <ul class="nav navbar-nav">
                  <li class="nav-item d-none d-lg-block">
                    <form action="{{ route('search.client.maf') }}" method="post">
                      @csrf
                      <input type="text" placeholder="MafNo" name="mafNo" class="form-control">
                    </form>
                  </li>
                </ul>
                <ul class="nav navbar-nav">
                  <li class="nav-item d-none d-lg-block">
                    <form action="{{ route('search.client.fclp') }}" method="post">
                      @csrf
                      <input type="text" placeholder="FCLP" name="fclp" class="form-control">
                    </form>
                  </li>
                </ul>
              </div>
              <ul class="nav navbar-nav float-right">
                <li class="dropdown dropdown-language nav-item">
                  <a class="dropdown-toggle nav-link" id="dropdown-flag" href="#" data-toggle="dropdown"
                     aria-haspopup="true" aria-expanded="false">
                    <i class="flag-icon flag-icon-us"></i>
                    <span class="selected-language">English</span>
                  </a>
                  <div class="dropdown-menu" aria-labelledby="dropdown-flag">
                    <a class="dropdown-item" href="{{url('lang/en')}}" data-language="en">
                      <i class="flag-icon flag-icon-us"></i>English
                    </a>
                    {{--                <a class="dropdown-item" href="{{url('lang/fr')}}" data-language="fr">--}}
                    {{--                  <i class="flag-icon flag-icon-fr"></i>French--}}
                    {{--                </a>--}}
                    {{--                <a class="dropdown-item" href="{{url('lang/de')}}" data-language="de">--}}
                    {{--                  <i class="flag-icon flag-icon-de"></i>German--}}
                    {{--                </a>--}}
                    {{--                <a class="dropdown-item" href="{{url('lang/pt')}}" data-language="pt">--}}
                    {{--                  <i class="flag-icon flag-icon-pt"></i>Portuguese--}}
                    {{--                </a>--}}
                  </div>
                </li>
                <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-expand"><i
                      class="ficon feather icon-maximize"></i></a></li>
                <li class="nav-item nav-search"><a class="nav-link nav-link-search"><i
                      class="ficon feather icon-search"></i></a>
                  <div class="search-input" id="navSearchClientDiv">
                    <div class="search-input-icon"><i class="feather icon-search primary"></i></div>
                    <input class="input" type="text" placeholder="Search Client...." id="navSearchClientInput"
                           tabindex="-1"
                           data-search="laravel-search-list"/>
                    <div class="search-input-close"><i class="feather icon-x"></i></div>
                    <ul class="search-list search-list-main"></ul>
                  </div>
                </li>
                <li class="dropdown dropdown-user nav-item"><a class="dropdown-toggle nav-link dropdown-user-link"
                                                               href="#"
                                                               data-toggle="dropdown">
                    <div class="user-nav d-sm-flex d-none"><span
                        class="user-name text-bold-600">{{ \Illuminate\Support\Facades\Auth::user()->name }}</span><span
                        class="user-status">Available</span></div>
                    <span>

                    @if(\Illuminate\Support\Facades\Auth::user()->employee)
                        <img class="round"
                             src="{{ \Illuminate\Support\Facades\Auth::user()->employee->photo }}" alt="avatar"
                             height="40"
                             width="40"/>
                      @elseif(\Illuminate\Support\Facades\Auth::user()->client)
                        <img class="round"
                             src="{{ avatar(\Illuminate\Support\Facades\Auth::user()->client->name) }}" alt="avatar"
                             height="40"
                             width="40"/>
                      @else
                        <img class="round"
                             src="{{ \Illuminate\Support\Facades\Auth::user()->client->photo }}" alt="avatar"
                             height="40"
                             width="40"/>

                      @endif

                    </span>


                  </a>
                  <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="{{ route('profile') }}"><i
                        class="feather icon-user"></i> Profile</a>
                    @if(\Illuminate\Support\Facades\Auth::user()->employee)
                    <a class="dropdown-item"
                       href="{{ route('employee.logs') }}"><i
                        class="feather icon-activity"></i> Logs</a>
                    @endif
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item"
                       onclick="event.preventDefault(); document.getElementById('frm-logout').submit();"><i
                        class="feather icon-power"></i> Logout</a>
                    <form id="frm-logout" action="{{ route('logout') }}" method="POST" style="display: none;">
                      {{ csrf_field() }}
                    </form>
                    @csrf
                    </form>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </nav>

      {{-- Search Start Here --}}
      <ul class="main-search-list-defaultlist d-none">
        <li class="d-flex align-items-center">
          <a class="pb-25" href="#">
            {{--            <h6 class="text-primary mb-0">Files</h6>--}}
          </a>
        </li>
        {{--        <li class="auto-suggestion d-flex align-items-center cursor-pointer">--}}
        {{--          <a class="d-flex align-items-center justify-content-between w-100" href="#">--}}
        {{--            <div class="d-flex">--}}
        {{--              <div class="ml-0 mr-50"><img src="{{ asset('images/icons/xls.png') }}" alt="png" height="32" />--}}
        {{--              </div>--}}
        {{--              <div class="search-data">--}}
        {{--                <p class="search-data-title mb-0">Two new item submitted</p><small class="text-muted">Marketing--}}
        {{--                  Manager</small>--}}
        {{--              </div>--}}
        {{--            </div><small class="search-data-size mr-50 text-muted">&apos;17kb</small>--}}
        {{--          </a>--}}
        {{--        </li>--}}
        {{--        <li class="auto-suggestion d-flex align-items-center cursor-pointer">--}}
        {{--          <a class="d-flex align-items-center justify-content-between w-100" href="#">--}}
        {{--            <div class="d-flex">--}}
        {{--              <div class="ml-0 mr-50"><img src="{{ asset('images/icons/jpg.png') }}" alt="png" height="32" />--}}
        {{--              </div>--}}
        {{--              <div class="search-data">--}}
        {{--                <p class="search-data-title mb-0">52 JPG file Generated</p><small class="text-muted">FontEnd--}}
        {{--                  Developer</small>--}}
        {{--              </div>--}}
        {{--            </div><small class="search-data-size mr-50 text-muted">&apos;11kb</small>--}}
        {{--          </a>--}}
        {{--        </li>--}}
        {{--        <li class="auto-suggestion d-flex align-items-center cursor-pointer">--}}
        {{--          <a class="d-flex align-items-center justify-content-between w-100" href="#">--}}
        {{--            <div class="d-flex">--}}
        {{--              <div class="ml-0 mr-50"><img src="{{ asset('images/icons/pdf.png') }}" alt="png" height="32" />--}}
        {{--              </div>--}}
        {{--              <div class="search-data">--}}
        {{--                <p class="search-data-title mb-0">25 PDF File Uploaded</p><small class="text-muted">Digital--}}
        {{--                  Marketing Manager</small>--}}
        {{--              </div>--}}
        {{--            </div><small class="search-data-size mr-50 text-muted">&apos;150kb</small>--}}
        {{--          </a>--}}
        {{--        </li>--}}
        {{--        <li class="auto-suggestion d-flex align-items-center cursor-pointer">--}}
        {{--          <a class="d-flex align-items-center justify-content-between w-100" href="#">--}}
        {{--            <div class="d-flex">--}}
        {{--              <div class="ml-0 mr-50"><img src="{{ asset('images/icons/doc.png') }}" alt="png" height="32" />--}}
        {{--              </div>--}}
        {{--              <div class="search-data">--}}
        {{--                <p class="search-data-title mb-0">Anna_Strong.doc</p><small class="text-muted">Web--}}
        {{--                  Designer</small>--}}
        {{--              </div>--}}
        {{--            </div><small class="search-data-size mr-50 text-muted">&apos;256kb</small>--}}
        {{--          </a>--}}
        {{--        </li>--}}
        {{--        <li class="d-flex align-items-center">--}}
        {{--          <a class="pb-25" href="#">--}}
        {{--            <h6 class="text-primary mb-0">Members</h6>--}}
        {{--          </a>--}}
        {{--        </li>--}}
        {{--        <li class="auto-suggestion d-flex align-items-center cursor-pointer">--}}
        {{--          <a class="d-flex align-items-center justify-content-between py-50 w-100" href="#">--}}
        {{--            <div class="d-flex align-items-center">--}}
        {{--              <div class="avatar mr-50"><img src="{{ asset('images/portrait/small/avatar-s-8.jpg') }}" alt="png"--}}
        {{--                                             height="32" /></div>--}}
        {{--              <div class="search-data">--}}
        {{--                <p class="search-data-title mb-0">John Doe</p><small class="text-muted">UI designer</small>--}}
        {{--              </div>--}}
        {{--            </div>--}}
        {{--          </a>--}}
        {{--        </li>--}}
        {{--        <li class="auto-suggestion d-flex align-items-center cursor-pointer">--}}
        {{--          <a class="d-flex align-items-center justify-content-between py-50 w-100" href="#">--}}
        {{--            <div class="d-flex align-items-center">--}}
        {{--              <div class="avatar mr-50"><img src="{{ asset('images/portrait/small/avatar-s-1.jpg') }}" alt="png"--}}
        {{--                                             height="32" /></div>--}}
        {{--              <div class="search-data">--}}
        {{--                <p class="search-data-title mb-0">Michal Clark</p><small class="text-muted">FontEnd--}}
        {{--                  Developer</small>--}}
        {{--              </div>--}}
        {{--            </div>--}}
        {{--          </a>--}}
        {{--        </li>--}}
        {{--        <li class="auto-suggestion d-flex align-items-center cursor-pointer">--}}
        {{--          <a class="d-flex align-items-center justify-content-between py-50 w-100" href="#">--}}
        {{--            <div class="d-flex align-items-center">--}}
        {{--              <div class="avatar mr-50"><img src="{{ asset('images/portrait/small/avatar-s-14.jpg') }}" alt="png"--}}
        {{--                                             height="32" /></div>--}}
        {{--              <div class="search-data">--}}
        {{--                <p class="search-data-title mb-0">Milena Gibson</p><small class="text-muted">Digital Marketing--}}
        {{--                  Manager</small>--}}
        {{--              </div>--}}
        {{--            </div>--}}
        {{--          </a>--}}
        {{--        </li>--}}
        {{--        <li class="auto-suggestion d-flex align-items-center cursor-pointer">--}}
        {{--          <a class="d-flex align-items-center justify-content-between py-50 w-100" href="#">--}}
        {{--            <div class="d-flex align-items-center">--}}
        {{--              <div class="avatar mr-50"><img src="{{ asset('images/portrait/small/avatar-s-6.jpg') }}" alt="png"--}}
        {{--                                             height="32" /></div>--}}
        {{--              <div class="search-data">--}}
        {{--                <p class="search-data-title mb-0">Anna Strong</p><small class="text-muted">Web Designer</small>--}}
        {{--              </div>--}}
        {{--            </div>--}}
        {{--          </a>--}}
        {{--        </li>--}}
      </ul>
      <ul class="main-search-list-defaultlist-other-list d-none">
        <li class="auto-suggestion d-flex align-items-center justify-content-between cursor-pointer">
          <a class="d-flex align-items-center justify-content-between w-100 py-50">
            <div class="d-flex justify-content-start"><span class="mr-75 feather icon-alert-circle"></span><span>No
           Clients found.</span></div>
          </a>
        </li>
      </ul>
    {{-- Search Ends --}}
    <!-- END: Header-->

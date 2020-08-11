@php
  $configData = Helper::applClasses();
@endphp
<div
  class="main-menu menu-fixed {{($configData['theme'] === 'light') ? "menu-light" : "menu-dark"}} menu-accordion menu-shadow"
  data-scroll-to-active="true">
  <div class="navbar-header">
    <ul class="nav navbar-nav flex-row">
      <li class="nav-item mr-auto">
        <a class="navbar-brand" href="dashboard-analytics">
          <div class="brand-logo"></div>
          <h2 class="brand-text mb-0">ForbClub</h2>
        </a>
      </li>
      <li class="nav-item nav-toggle">
        <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
          <i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i>
          <i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block primary collapse-toggle-icon"
             data-ticon="icon-disc">
          </i>
        </a>
      </li>
    </ul>
  </div>
  <div class="shadow-bottom"></div>
  <div class="main-menu-content">
    <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
      {{-- Foreach menu item starts --}}
      @if(isset($menuData[0]))


        @foreach($menuData[0]->menu as $menu)
          @if(isset($menu->navheader))
            <li class="navigation-header">
              <span>{{ $menu->navheader }}</span>
            </li>
          @elseif(isset($menu->role) || isset($menu->permission))
            @hasanyrole($menu->role)
              @include('panels.navItems',['menu'=>$menu])
            @endrole
            @can($menu->permission)
              @include('panels.navItems',['menu'=>$menu])
            @endcan
          @else
            {{-- Add Custom Class with nav-item --}}
            @include('panels.navItems',['menu'=>$menu])
          @endif
        @endforeach
      @endif
      {{-- Foreach menu item ends --}}
    </ul>
  </div>
</div>
<!-- END: Main Menu-->

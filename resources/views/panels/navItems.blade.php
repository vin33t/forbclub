@php
  $custom_classes = "";
  if(isset($menu->classlist)) {
  $custom_classes = $menu->classlist;
  }
  $translation = "";
  if(isset($menu->i18n)){
  $translation = $menu->i18n;
  }
@endphp

<li class="nav-item {{ (request()->is($menu->url)) ? 'active' : '' }} {{ $custom_classes }}">
  <a href="/{{ $menu->url }}">
    <i class="{{ $menu->icon }}"></i>
    <span class="menu-title" data-i18n="{{ $translation }}">{{ __('locale.'.$menu->name) }} </span>
    @if (isset($menu->badge))
      <?php $badgeClasses = "badge badge-pill badge-primary float-right" ?>
      <span
        class="{{ isset($menu->badgeClass) ? $menu->badgeClass.' test' : $badgeClasses.' notTest' }} ">{{$menu->badge}}</span>
    @endif
  </a>
  @if(isset($menu->submenu))
    @include('panels/submenu', ['menu' => $menu->submenu])
  @endif
</li>

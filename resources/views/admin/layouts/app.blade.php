<!doctype html>
<html lang="en">

<head>
  @include('admin.layouts.partials.head')
</head>

<body data-topbar="dark">

  <div id="layout-wrapper">
    @include('admin.layouts.partials.header')
    @include('admin.layouts.partials.sidebar')

    <div class="main-content">
      @yield('content')
      {{-- Footer --}}
      @include('admin.layouts.partials.footer')
    </div>

  </div>

  @include('admin.layouts.partials.scripts')
</body>

</html>

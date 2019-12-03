<!DOCTYPE html>
<html>

@include('partials.head')

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  @include('partials.navbar')
  @include('partials.sidebar')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    @yield('content')
  </div>
  <!-- /.content-wrapper -->

  @include('partials.footer')

</div>
<!-- ./wrapper -->

@include('partials.scripts')

</body>

</html>

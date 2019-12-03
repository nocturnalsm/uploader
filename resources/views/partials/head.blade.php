<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Uploader</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/fontawesome-free/css/all.min.css') }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('AdminLTE/dist/css/adminlte.min.css') }}">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <!-- Alert Plugin -->
  <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/sweetalert2/sweetalert2.min.css') }}">
  @stack('css')
</head>
<style>
    .choices {
      margin-bottom: 0;
    }
    .choices[data-type*=select-multiple] .choices__inner {
      max-height: 105px;
      overflow: auto;
    }
    .dataTables_wrapper .dataTables_processing {
      font-weight: 600;
      top: 10% !important;
    }
    .dataTables_wrapper .dataTables_processing::before {
      content: "\f110";
      font-family: "Font Awesome 5 Free"; 
      font-weight: 900;
      display: block;
      font-size: 130%;
    }
    .sidebar-mini .sidebar .image {
      display: none !important;
    }
    
    .sidebar-mini.sidebar-collapse .sidebar .image {
      display: block !important;
    }
    .sidebar-mini.sidebar-collapse .main-sidebar:hover .sidebar .image {
      display: none !important;
    }
</style>
@stack('stylesheets')
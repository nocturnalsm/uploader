@extends('layouts.admin')
@push('css')
  <link rel="stylesheet" href="{{ asset('choicesjs/choices.min.css') }}">  
@endpush
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Pengaturan
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Pengaturan</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">      
      <div class="row">
        <div class="col-12">                    
          <div class="card card-primary">
            <form id="formsetting" method="POST" action="{{ route('settings.store') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            @hasrole('Super Admin')
            <div class="card-header">
                Pengaturan Upload
            </div>
            <div class="card-body">
                <div class="form-group row">
                  <label class="col-form-label col-md-4">Username Google Drive</label>
                  <div class="col-md-8">
                      <input type="email" name="google_drive_username" id="google_drive_username" class="form-control{{ $errors->has('google_drive_username') ? ' is-invalid' : '' }}" value="{{ $app_settings['google_drive_username'] }}">                    
                  </div>
                  @if ($errors->has('google_drive_username'))
                      <div class="invalid-feedback d-block offset-md-4 px-2" role="alert">
                          <strong>{{ $errors->first('google_drive_username') }}</strong>
                      </div>
                  @endif
                </div>
                <div class="form-group row">
                  <label class="col-form-label col-md-4">Folder Google Drive</label>
                  <div class="col-md-8">
                        <input type="text" name="google_drive_upload_folder" id="google_drive_upload_folder" class="form-control{{ $errors->has('google_drive_upload_folder') ? ' is-invalid' : '' }}" value="{{ $app_settings['google_drive_upload_folder'] }}">
                  </div>
                  @if ($errors->has('google_drive_upload_folder'))
                      <div class="invalid-feedback d-block offset-md-4 px-2" role="alert">
                          <strong>{{ $errors->first('google_drive_upload_folder') }}</strong>
                      </div>
                  @endif
                </div>
            </div>
            <!-- /.card-body -->
            @endhasrole
            <div class="card-header">
                Pengaturan Umum
            </div>
            <div class="card-body">
                <div class="form-group row">
                  <label class="col-form-label col-md-4">Nama Perusahaan</label>
                  <div class="col-md-4">
                    <select class="form-control{{ $errors->has('current_company') ? ' is-invalid' : '' }}" name="current_company" id="current_company">
                    @foreach($companies as $comp)
                        <option {{ $user_settings['current_company'] == $comp->COMPANY_ID ? "selected " : "" }}value="{{ $comp->COMPANY_ID }}">{{ $comp->NAME}}</option>
                    @endforeach
                    </select>
                  </div>
                  @if ($errors->has('current_company'))
                      <div class="invalid-feedback d-block offset-md-4 px-2" role="alert">
                          <strong>{{ $errors->first('current_company') }}</strong>
                      </div>
                  @endif
                </div>
                <div class="form-group row">
                  <label class="col-form-label col-md-4">Jumlah Notifikasi yang Ditampilkan</label>
                  <div class="col-md-4">
                      <input max="10" type="number" name="notification_menu_max" id="notification_menu_max" class="form-control{{ $errors->has('notification_menu_max') ? ' is-invalid' : '' }}" value="{{ $user_settings['notification_menu_max'] }}">                        
                  </div>
                  @if ($errors->has('notification_menu_max'))
                      <div class="invalid-feedback d-block offset-md-4 px-2" role="alert">
                          <strong>{{ $errors->first('notification_menu_max') }}</strong>
                      </div>
                  @endif
                </div>
                <button type="submit" class="btn btn-primary">
                     Simpan Pengaturan
                </button>                
            </div>
            <!-- /.card-body -->
          </div> 
          </form>
          <form id="formpassword" method="POST" action="{{ route('settings.resetpassword') }}">
          <!-- /.card -->
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <div class="card card-primary card-sm">
            <div class="card-header">
                Ganti Password
            </div>
            <div class="card-body">
                <div class="form-group row">
                  <label class="col-form-label col-md-4">Password Baru</label>
                  <div class="col-md-4">
                      <input type="password" name="password" id="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" value="">
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-form-label col-md-4">Ketik Password Baru Sekali Lagi</label>
                  <div class="col-md-4">
                        <input type="password" name="confirm" id="confirm" class="form-control" value="">
                  </div>
                </div>
                @if ($errors->has('password'))
                      <div class="invalid-feedback d-block offset-md-4 px-2" role="alert">
                          <strong>{{ $errors->first('password') }}</strong>
                      </div>
                @endif                
                <button type="submit" class="btn btn-primary">
                    Ganti Password
                </button>                
            </div>
            <!-- /.card-body -->
            </form>
          </div> 
          <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
      <!-- /.row -->
</section>
    <!-- /.content -->
<div id="flash"></div>
@endsection

@push('scripts_end')
<script src="{{ asset('choicesjs/choices.min.js') }}"></script>
<script>
    var choices_company = new Choices("#current_company", {removeItemButton: true});
</script>
@if (session('type'))
@include('partials.flash', ["type" => session('type'), "text" => session('text')])
@endif
@endpush



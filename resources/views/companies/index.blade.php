@extends('layouts.admin')
@push('css')
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/datatables/dataTables.bootstrap4.css') }}">
@endpush
@push('stylesheets')
  <style>
      .modal-xl {width:80%;}
  </style>
@endpush
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Perusahaan
            @can('company.create')
            &nbsp;&nbsp;<button id="add" class="btn btn-primary">Tambah Perusahaan</button>
            @endcan
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
              <li class="breadcrumb-item active">Perusahaan</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      @include('partials.inputmodal',["modalsize" => "modal-lg"])
      <div class="row">
        <div class="col-12">          
          <div class="card">
            <div class="card-body">
              @include('partials.search')
              <table id="table" class="table table-striped table-sm table-hover">
                <thead>
                <tr>
                  <th class="border-top-0">Jenis</th>
                  <th class="border-top-0">Nama Perusahaan</th>
                  <th class="border-top-0">Kota</th>
                  <th class="border-top-0">Aktif</th>
                  <th class="border-top-0">Aksi</th>
                </tr>
                </thead>
                <tbody>                
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
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

@push('yesdelete')
    var id = $(this).attr("data-id");
    $.ajax({
        method: "DELETE",
        data: {"_token": "{{ csrf_token() }}","id": id},
        url: "{{ url('master/company/delete') }}",
        success: function(response){
                flashMessage(response);
                table.draw();            
        },
        error: function(jqXHR, textStatus, errorThrown){
            flashMessage(jqXHR.responseText);
        }
    })
@endpush

@push('scripts_end')
<!-- DataTables -->
<script src="{{ asset('AdminLTE/plugins/datatables/jquery.dataTables.js') }}"></script>
<script src="{{ asset('AdminLTE/plugins/datatables/dataTables.bootstrap4.js') }}"></script>
<script src="{{ asset('AdminLTE/plugins/inputmask/jquery.inputmask.bundle.js') }}"></script>
<script>
  $(function () {
    function flashMessage(response){
        $("#flash").html("");
        $("#flash").html(response);
    }
    window.table = $('#table').DataTable({
      "paging": true,
      "dom": "rtip",
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "processing": true,
      "serverSide": true,
      "ajax": {
          url: "{{ route('company.index') }}"
      },
      "columns": [
            { "data"  : "COMPANY_TYPE" },
            { "data"  : "NAME" },
            { "data"  : "KOTA" },
            { "data"  : "AKTIF",
              "render": function(data,type){                  
                  if (data == "Y"){
                        return '<i class="fa fa-check-circle text-success"></i>';
                  }
                  else {
                        return '<i class="fa fa-times-circle text-danger"></i>';
                  }
              }
            },
            {
                "data": 'ACTION',
                "sortable": false
            }
        ]
    });
    $("#add").on("click", function(){
        $.ajax({
            url: "{{ route('company.create') }}",
            method: "GET",
            success: function(response){
                $("#modalform .modal-body").html("");
                $("#modalform .modal-title").html("Tambah Perusahaan");
                $("#modalform .modal-body").html(response);
                $("#modalform").modal("show");
                $("#nama").focus();
            },
            error: function(jqXHR, textStatus, errorThrown){
                flashMessage(jqXHR.responseText);
            }
        })
    })
    $("body").on("submit", "#form", function(e){
        e.preventDefault();        
        var data = $("#form").serialize();        
        $.ajax({
            url: $(this).attr("action"),
            method: $("#form input[name=action]").val() == "edit" ? "PUT" : "POST",
            data: $("#form").serialize(),
            success: function(response){                
                if (response.errors){
                    var errors = Object.keys(response.errors);
                    $(errors).each(function(index, elem){
                        $("#" +elem).addClass("is-invalid");
                        $("#" +elem).next(".invalid-feedback").html(response.errors[elem][0]);
                    })
                    $("#form .is-invalid").first().focus();
                }
                else {
                    flashMessage(response);
                    if ($("#form input[name=action]").val() == "add"){
                        $("#add").trigger("click");
                    }
                    else {
                        $("#modalform").modal("hide");
                    }
                    table.draw();
                }
            }
        })
    });
    $("body").on("input", "form input,select", function(){
        $(this).removeClass("is-invalid");        
        $(this).next(".invalid-feedback").html("");
    });
    $("body").on("click",".btn-edit", function(){
        $.ajax({
            url: "{{ route('company.index') }}/" + $(this).attr("data-id") +"/edit",            
            method: "GET",
            success: function(response){
                $("#modalform .modal-body").html("");
                $("#modalform .modal-title").html("Edit Perusahaan");
                $("#modalform .modal-body").html(response);
                $("#modalform").modal("show");
                $("#nama").focus();
            },
            error: function(jqXHR, textStatus, errorThrown){
                flashMessage(jqXHR.responseText);
            }
        })
    })
    $("body").on("click",".btn-delete", function(){
        @include("partials.deletedialog")        
    })
    $("body").on("click", "#add_direktur", function(){
        var html = $("#template_direktur").html();
        $("#datadirektur").append(html);
        $(".direktur").last().find(".input_npwp").inputmask("99.999.999.9-999.999");
    })
    $("body").on("click", "#add_komisaris", function(){
        var html = $("#template_komisaris").html();
        $("#datakomisaris").append(html);
        $(".komisaris").last().find(".input_npwp").inputmask("99.999.999.9-999.999");
    });
    $("body").on("click", ".hapus_komisaris", function(){        
        var div = $(this).closest(".komisaris");
        var value = $(div).find("input[name='komisaris_id[]']").val();
        var deletedvalue = $("input[name=deletekom]").val();
        if (deletedvalue == ""){
          deletedvalue = value;
        }
        else {
          deletedvalue += "," + value;
        }
        console.log(deletedvalue);
        $("input[name=deletekom]").val(deletedvalue);
        $(div).remove();        
        
    })
    $("body").on("click", ".hapus_direktur", function(){
        var div = $(this).closest (".direktur");
        var value = $(div).find("input[name='direktur_id[]']").val();
        var deletedvalue = $("input[name=deletedir]").val();
        if (deletedvalue == ""){
          deletedvalue = value;
        }
        else {
          deletedvalue += "," + value;
        }        
        $("input[name=deletedir]").val(deletedvalue);
        $(div).remove();                
    })
    $('input[name=searchinput]').on( 'search', function () {   
        window.table.search($(this).val()).draw();
    });
  });
</script>
@endpush


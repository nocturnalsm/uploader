@extends('layouts.admin')
@push('css')
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/datatables/dataTables.bootstrap4.css') }}">
@endpush
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Tipe Dokumen
            @if (auth()->user()->can('documenttype.create'))
            &nbsp;&nbsp;<button id="add" class="btn btn-primary">Tambah Tipe Dokumen</button>
            @endif
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Tipe Dokumen</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      @include('partials.inputmodal')
      <div class="row">
        <div class="col-12">          

          <div class="card">
            <div class="card-body">
              <table id="table" class="table table-sm table-hover table-striped">
                <thead>
                <tr>
                  <th class="border-top-0">Tipe</th>
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
        url: "{{ url('master/documenttype/delete') }}",
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
<script>
  $(function () {
    function flashMessage(response){
        $("#flash").html("");
        $("#flash").html(response);
    }
    var table = $('#table').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "processing": true,
      "serverSide": true,
      "ajax": {
          url: "{{ route('documenttype.index') }}"
      },
      "columns": [
            { "data"  : "DOCUMENT_TYPE" },
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
                "data": "ACTION",
                "sortable": false
            }
        ]
    });
    $("#add").on("click", function(){
        $.ajax({
            url: "{{ route('documenttype.create') }}",
            method: "GET",
            success: function(response){
                $("#modalform .modal-body").html("");
                $("#modalform .modal-title").html("Tambah Tipe Dokumen");
                $("#modalform .modal-body").html(response);
                $("#modalform").modal("show");
                $("#tipe").focus();
            },
            error: function(jqXHR, textStatus, errorThrown){
                flashMessage(jqXHR.responseText);
            }            
        })
    })
    $("body").on("submit", "#form", function(e){
        e.preventDefault();
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
        $("#tipe").next(".invalid-feedback").html("");
    });
    $("body").on("click",".btn-edit", function(){
        $.ajax({
            url: "{{ route('documenttype.index') }}/" + $(this).attr("data-id") +"/edit",            
            method: "GET",
            success: function(response){
                $("#modalform .modal-body").html("");
                $("#modalform .modal-title").html("Edit Tipe Dokumen");
                $("#modalform .modal-body").html(response);
                $("#modalform").modal("show");
                $("#tipe").focus();
            },
            error: function(jqXHR, textStatus, errorThrown){
                flashMessage(jqXHR.responseText);
            }
        })
    })
    $("body").on("click",".btn-delete", function(){
        @include("partials.deletedialog")        
    })
    $("body").on("change", "#aktif", function(){
        if ($(this).prop("checked")){
            $(this).next("label").html("Ya");
        }
        else {
            $(this).next("label").html("Tidak");
        }
    })
  });
</script>
@endpush


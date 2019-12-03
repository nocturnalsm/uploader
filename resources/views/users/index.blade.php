@extends('layouts.admin')
@push('css')
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/datatables/dataTables.bootstrap4.css') }}">
  <link rel="stylesheet" href="{{ asset('choicesjs/choices.min.css') }}">
@endpush
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>User
            &nbsp;&nbsp;<button id="add" class="btn btn-primary">Tambah User</button>
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
              <li class="breadcrumb-item active">User</li>
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
              @include('partials.search')
              <table id="table" class="table table-striped table-sm table-hover" width="100%">
                <thead>
                <tr>
                  <th class="border-top-0">Username</th>
                  <th class="border-top-0">Nama</th>
                  <th class="border-top-0">Email</th>
                  <th class="border-top-0">Group Ijin</th>
                  <th class="border-top-0">Upload Terakhir</th>
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
        url: "{{ url('admin/users/delete') }}",
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
<script src="{{ asset('choicesjs/choices.min.js') }}"></script>
<script>
  $(function () {
    window.table = $('#table').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "dom": "rtip",
      "autoWidth": false,
      "processing": true,
      "serverSide": true,
      "ajax": {
          url: "{{ route('users.index') }}"
      },
      "columns": [
            { "data"  : "username" },
            { "data"  : "name" },
            { "data"  : "email" },            
            { "data"  : "roles",
              "render": function(data,type){
                  var str_roles = "";
                  if (data){
                    $.each(data.split(","), function(index,elem){
                        str_roles = str_roles + '<span class="badge badge-primary">' +
                                    elem + '</span>&nbsp;';
                    })
                  }
                  return str_roles;
              } 
            },
            { "data"  : "last_upload" },
            { "data"  : "aktif",
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
                "data": null,
                "sortable": false,
                "render": function(data, type, row){                    
                    return '<button class="btn btn-sm btn-primary btn-edit" data-id="' + 
                            row.id +'"> ' +
                           '<i class="fa fa-edit"></i>&nbsp;Edit' +
                           '</button>&nbsp;&nbsp;' +
                           '<button class="btn btn-sm btn-danger btn-delete" data-id="' +
                            row.id +'"> ' +
                           '<i class="fa fa-trash"></i>&nbsp;Hapus' +
                           '</button>';
                }
            }
        ]
    });
    $("#add").on("click", function(){
        $.ajax({
            url: "{{ route('users.create') }}",
            method: "GET",
            success: function(response){
                $("#modalform .modal-body").html("");
                $("#modalform .modal-title").html("Tambah User");
                $("#modalform .modal-body").html(response);
                $("#modalform").modal("show");
                $("#name").focus();
            },
            error: function(jqXHR, textStatus, errorThrown){
                flashMessage(jqXHR.responseText);
            }
        })
    })
    function flashMessage(response){
        $("#flash").html("");
        $("#flash").html(response);
    }
    $("body").on("submit", "#form", function(e){
        e.preventDefault();
        $.ajax({
            url: $("#form").attr("action"),
            method: $("#form input[name=action]").val() == "edit" ? "PUT" : "POST",
            data: $("#form").serialize(),
            success: function(response){                
                if (response.errors){
                    var errors = Object.keys(response.errors);                    
                    $(errors).each(function(index, elem){
                        if ($("#" +elem).hasClass("choices__input")){
                            $("#" +elem).closest(".choices").addClass("is-invalid");
                            $("#" +elem).closest(".choices").siblings(".invalid-feedback").html(response.errors[elem][0]).show();
                        }
                        else {
                          $("#" +elem).addClass("is-invalid");
                          $("#" +elem).next(".invalid-feedback").html(response.errors[elem][0]);
                        }
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
    $("body").on("input", "form input,select,.choices", function(){
        $(this).removeClass("is-invalid");        
        $(this).next(".invalid-feedback").html("");
    });
    $("body").on("change", ".choices", function(){
        $(this).removeClass("is-invalid");        
        $(this).next(".invalid-feedback").html("");
    })
    $("body").on("click",".btn-edit", function(){
        $.ajax({
            url: "{{ route('users.index') }}/" + $(this).attr("data-id") +"/edit",            
            method: "GET",
            success: function(response){
                $("#modalform .modal-body").html("");
                $("#modalform .modal-title").html("Edit User");
                $("#modalform .modal-body").html(response);
                $("#modalform").modal("show");
                $("#name").focus();
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
    });
    $('input[name=searchinput]').on( 'search', function () {    
        window.table.search($(this).val()).draw();
    });
  });
</script>
@endpush


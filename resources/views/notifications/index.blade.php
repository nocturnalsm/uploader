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
            <h1>Notifikasi
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
              <li class="breadcrumb-item active">Notifikasi</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">      
      <div class="row">
        <div class="col-12">          

          <div class="card">
            <div class="card-body">
              @include('partials.search') 
              <table id="table" class="table table-striped table-sm table-hover">
                <thead>
                <tr>
                  <th class="border-top-0">Waktu</th>
                  <th class="border-top-0">Notifikasi</th>
                  <th class="border-top-0">URL</th>
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
        url: "{{ url('notifications/delete') }}",
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
          url: "{{ route('notifications.index') }}"
      },
      "columns": [
            { "data"  : "created_at"},
            { "data"  : "NOTIFICATION" },
            { "data"  : "URL",
              "render": function(data,type){   
                    if (data == null){
                        return "";
                    }
                    else if (data != ""){
                        return '<a href="' + data + '">' + data + '</a>';
                    }
                    else {
                        return "";
                    }
              } 
            },            
            {
                "data": null,
                "sortable": false,
                "render": function(data, type, row){                    
                    return '<button class="btn btn-sm btn-danger btn-delete" data-id="' +
                            row.NOTIFICATION_ID +'"> ' +
                           '<i class="fa fa-trash"></i>&nbsp;Hapus' +
                           '</button>';
                }
            }
        ]
    });
    $("body").on("click",".btn-delete", function(){
        @include("partials.deletedialog")        
    })
    $('input[name=searchinput]').on( 'keyup', function () {    
        window.table.search($(this).val()).draw();
    });
  });
</script>
@endpush


@extends('layouts.admin')
@push('css')
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/datatables/dataTables.bootstrap4.css') }}">
  <link rel="stylesheet" href="{{ asset('lightgallery/css/lightgallery.min.css') }}">
@endpush
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Home
            </h1>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">      
      <div class="row">
        <div class="col-12">          

          <div class="card card-primary">
            <div class="card-header">
                <h5 class="card-title">10 Upload Terakhir</h5>
            </div>
            <div class="card-body">
              <table id="table" class="table table-striped table-sm table-hover">
                <thead>
                <tr>
                  <th class="border-top-0">Nama Dokumen</th>
                  <th class="border-top-0">Folder</th>
                  <th class="border-top-0">Tgl Upload</th>
                  @hasrole('Super Admin')
                  <th class="border-top-0">User</th>
                  @endhasrole
                </tr>
                </thead>
                <tbody>                
                    @foreach($last_uploads as $upload)
                    <tr>
                        <td>{!! $upload->DOCUMENT_NAME !!}</td>
                        <td>{{ $upload->FOLDER_NAME }}</td>
                        <td>{{ $upload->upload_date }}</td>
                        @hasrole('Super Admin')
                        <td>{{ $upload->username }}</td>
                        @endhasrole
                    </tr>
                    @endforeach
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

@push('scripts_end')
<!-- DataTables -->
<script src="{{ asset('AdminLTE/plugins/datatables/jquery.dataTables.js') }}"></script>
<script src="{{ asset('AdminLTE/plugins/datatables/dataTables.bootstrap4.js') }}"></script>
<script src="{{ asset('lightgallery/js/lightgallery-all.min.js') }}"></script>
<script>
  $(function () {
    window.table = $('#table').DataTable({
      "paging": false,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": false,
      "autoWidth": false,
      "processing": false,
      "serverSide": false,
      "order": [[1,"desc"]]
    });
    $("body").on("click","a.file-preview", function(){
      var dynamic = [];
      var index = $("a.file-preview").index($(this)); 
      $.each($("a.file-preview"), function(index, elem){
          dynamic.push(
            {src: "{{ route('document.get') }}?id=" + $(elem).attr("data-id"),
             iframe: $(elem).attr("data-type") == "application/pdf"
            }
          );
      })
      $(this).lightGallery({dynamic: true, thumbnail: false,
                            index: index,
                            dynamicEl: dynamic});
    });
    $('input[name=searchinput]').on( 'keyup', function () {    
        window.table.search($(this).val()).draw();
    });
  });
</script>
@endpush


@extends('layouts.admin')
@push('css')
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/datatables/dataTables.bootstrap4.css') }}">
  <link rel="stylesheet" href="{{ asset('choicesjs/choices.min.css') }}">  
  <link rel="stylesheet" href="{{ asset('lightgallery/css/lightgallery.min.css') }}">
  <link rel="stylesheet" href="{{ asset('dropzone/dropzone.min.css') }}">
@endpush
@push('stylesheets')
<style>
    #previews {
        border: 1px solid grey;        
        position: absolute;
        z-index: 2000;
        width: 500px;
        background-color: white;
        bottom: 20px;right:20px;
        display: none;
    }
    .file-row > div {
      display: inline-block;
      vertical-align: top;
      padding: 8px;
    }
    #previews .dz-progress .dz-upload {
        background: #333;
        background: linear-gradient(to bottom, #666, #444);
        position: absolute;
        top: 5%;        
        bottom: 0;
        height: 12px;
        width: 0;
        -webkit-transition: width 300ms ease-in-out;
        -moz-transition: width 300ms ease-in-out;
        -ms-transition: width 300ms ease-in-out;
        -o-transition: width 300ms ease-in-out;
        transition: width 300ms ease-in-out; 
    }
    #preview-container {
        max-height: 220px;
        overflow: auto;
    }
    #foldertree {
        background-color: transparent;
    }
</style>
@endpush
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Dokumen
            @if (auth()->user()->can('document.create'))
            <a title="Tambah Folder" id="addfolder" href="#" class="btn btn-primary"><i class="fa fa-folder-plus"></i><span class="d-md-none d-lg-inline">&nbsp;Tambah Folder</span></a>
            <a title="Upload Dokumen" id="add" href="#" class="btn btn-primary"><i class="fa fa-upload"></i><span class="d-md-none d-lg-inline">&nbsp;Upload Dokumen</span></a>
            @endif
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
              <li class="breadcrumb-item active">Dokumen</li>
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
        <div class="col-sm-12 px-0">
                 
          </div>
          <div class="card">
            <div class="card-body" id="uploader">
              <div class="row">
                <div class="col-md-8">
                  <ol id="foldertree" class="breadcrumb">
                    <li data-id="0" class="breadcrumb-item"><a href="#">Root</a></li>
                  </ol>
                  <span class="d-none" id="searchtext"> 
                  </span>
                </div>
                <div class="col-md-4">
                  @include('partials.search')
                </div> 
              </div>             
              <table id="table" class="table table-sm table-hover" width="100%">
                <thead>
                <tr>
                  <th class="border-top-0">Nama Dokumen</th>
                  @hasrole('Super Admin')
                  <th class="border-top-0">Diupload oleh</th>
                  @endhasrole
                  <th class="border-top-0">Tgl Upload</th>
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
<div id="previews" class="card">
    <div class="card-header">
      Upload File
      <span class="float-right">
        <a id="preview-close" href="#">
          <i class="fa fa-times"></i>
        </a>
      </span>
    </div>
    <div id="preview-container" class="card-body">
    </div>
</div>
@endsection

@push('yesdelete')
    var id = $(this).attr("data-id");
    var type = $(this).attr("data-type");
    if (type == "folder"){
      var url = "{{ url('master/folder/delete') }}";
    }
    else if (type == "document"){
      var url = "{{ url('document/delete') }}";
    }
    $.ajax({
        method: "DELETE",
        data: {"_token": "{{ csrf_token() }}","id": id},
        url: url,
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
<script src="{{ asset('lightgallery/js/lightgallery-all.min.js') }}"></script>
<script src="{{ asset('dropzone/dropzone.min.js') }}"></script>
<script>
  $(function () {
    window.table = $('#table').DataTable({
      "paging": false,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "dom": "rtip",
      "autoWidth": false,
      "processing": true,
      "serverSide": true,
      "ajax": {
          url: "{{ route('document.index') }}"
      },
      "columns": [
            { "data"  : "DOCUMENT_NAME" },          
            @hasrole('Super Admin')
            { "data"  : "username" },            
            @endhasrole
            { "data"  : "TGL_UPLOAD" },
            {
                "data": "ACTION",
                "sortable": false                
            }
        ]
    });
    function flashMessage(response){
        $("#flash").html("");
        $("#flash").html(response);
    }
    $("body").on("input", "form input,select,.choices", function(){
        $(this).removeClass("is-invalid");        
        $(this).next(".invalid-feedback").html("");
    });
    $("body").on("change", ".choices", function(){
        $(this).removeClass("is-invalid");        
        $(this).next(".invalid-feedback").html("");
    })
    $("body").on("click",".btn-edit", function(){
        var type = $(this).attr("data-type");
        if (type == "folder"){
          var url = "{{ route('folder.index') }}/" + $(this).attr("data-id") +"/edit";
        }
        else if (type == "document"){
          var url = "{{ route('document.index') }}/" + $(this).attr("data-id") +"/edit";
        }
        $.ajax({
            url: url,            
            method: "GET",
            success: function(response){
                $("#modalform .modal-body").html("");
                $("#modalform .modal-title").html("Edit " + (type == "folder" ? "Folder" : "Dokumen"));
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
    })    
    $("body").on("click","a.folder-preview", function(){
        var folder = $(this).attr("data-id");
        $("#foldertree").append('<li data-id="' +folder + '" class="breadcrumb-item"><a href="#">' + $(this).html() +'</a></li>');
        table.ajax.url("{{ route('document.index') }}?folder=" + folder).load();
    })
    $("body").on("click", "#foldertree li a", function(){
        var folder = $(this).closest("li").attr("data-id");
        var index = $("#foldertree li").index($(this).closest("li"));
        $("#foldertree li").slice(index+1).remove();
        table.ajax.url("{{ route('document.index') }}?folder=" + folder).load();
    });
    $("#addfolder").on("click", function(){
        $.ajax({
            url: "{{ route('folder.create') }}",
            method: "GET",
            data: {parent: $("#foldertree li").last().attr("data-id")},
            success: function(response){
                $("#modalform .modal-body").html("");
                $("#modalform .modal-title").html("Tambah Folder");
                $("#modalform .modal-body").html(response);
                $("#modalform").modal("show");
                $("#name").focus();
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
                       $("#addfolder").trigger("click");
                    }
                    else {
                        $("#modalform").modal("hide");
                    }
                    table.draw();
                }
            }
        })
    });
    $("body").on("change", "#aktif", function(){
        if ($(this).prop("checked")){
            $(this).next("label").html("Ya");
        }
        else {
            $(this).next("label").html("Tidak");
        }
    })
    $("body").on("dblclick","#table tbody tr", function(){
        $(this).find(".btn-edit").trigger("click");
    })
    $("#add").on("click", function(){
        $("#uploader").get(0).dropzone.hiddenFileInput.click();
    })        
    $("#preview-close").on("click", function(){
        $("#previews").hide();
    })
    var myDropzone = new Dropzone("#uploader", { 
          url: "{{ route('document.upload') }}",
	        maxFiles: 10,
            maxFilesize: 5,
            uploadMultiple: true,
            parallelUploads: 5,            
            previewsContainer: "#preview-container",
            previewTemplate: $("#template").html(),
            dictResponseError: 'Server not Configured',
            acceptedFiles: ".jpg,.jpeg,.gif,.png,.pdf",
            init:function(){	                 
                var self = this;                
                self.on("sendingmultiple", function (files, xhr, formData) {    
                    formData.append("_token", "{{ csrf_token() }}");	     
                    formData.append("folder", $("#foldertree li").last().attr("data-id"));
                    $("#previews").show();               
                });
                self.on("error", function (file, response) {                             
                    $(file.previewElement).find('.dz-error-mark').attr("title", response);
                });		
                self.on("completemultiple", function (files) {
                    var success = false;  
                    $.each(files, function(index,elem){
                        if(elem.status == Dropzone.SUCCESS){
                            success = true;
                            $(elem.previewElement).find(".dz-success-mark").html('<i class="fa fa-check-circle text-success">');
                            $(elem.previewElement).find(".dz-error-mark").hide();
                            $(elem.previewElement).find(".dz-progress").hide();
                        }
                        else if (elem.status == Dropzone.ERROR){
                          $(elem.previewElement).find('.dz-error-mark').html('<i class="fa fa-times-circle text-danger"></i>');                          
                          $(elem.previewElement).find(".dz-success-mark").hide();
                          $(elem.previewElement).find(".dz-progress").hide();
                        }
                    })
                    if (success == true){
                        table.draw();
                    }
                    var myDiv = $("#preview-container");
                    myDiv.animate({ scrollTop: myDiv.prop("scrollHeight") - myDiv.height() }, 600);                    
                });		
            }
    });
    $('input[name=searchinput]').on( 'search', function () {       
        var value = $(this).val();
        if (value != ""){
            $("#searchtext").html("Mencari dokumen dengan nama `" + value + "`");
            $("#searchtext").removeClass("d-none");
            $("#foldertree").hide();
        }
        else {
            $("#searchtext").addClass("d-none");
            $("#foldertree").show();
        }
        window.table.search(value).draw();
    });
});
</script>
<script type="text/template" id="template">
  @include('documents.uploader')
</script>
@endpush


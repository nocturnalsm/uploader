@extends('layouts.admin')
@push('css')
  <link rel="stylesheet" href="{{ asset('choicesjs/choices.min.css') }}">  
  <link rel="stylesheet" href="{{ asset('dropzone/dropzone.min.css') }}">
@endpush
@push('stylesheets')
<style>
    #previews {
        border: 1px solid grey;
        min-height: 160px;
        width: 100%;
    }
    .file-row > div {
      display: inline-block;
      vertical-align: top;
      border-top: 1px solid #ddd;
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
</style>
@endpush
@section('content')
<!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Upload Dokumen
            &nbsp;&nbsp;
            <button id="browse" type="button" class="btn btn-primary">Browse</button>
            <button id="upload" type="button" class="btn btn-primary disabled">Upload</button>
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('document.index') }}">Dokumen</a></li>
              <li class="breadcrumb-item active">Upload</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      @include('partials.inputmodal')
    
      <div class="row">
          <div class="col-sm-12 col-md-12 col-xs-12">   
            <span class="text-center mt-4">Drag file ke kotak di bawah ini untuk meng-upload atau klik Browse untuk memilih dokumen (.jpg|.jpeg|.gif|.png|.pdf).</span>         
            <div class="files px-4 w-100" id="previews">              
            </div>                          
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
<script src="{{ asset('dropzone/dropzone.min.js') }}"></script>
<script>
  $(function () {
    function flashMessage(response){
        $("#flash").html("");
        $("#flash").html(response);
    }
    $("#upload").on("click", function(){    
        var queued = myDropzone.getQueuedFiles();
        if (queued.length > 0){
          var names = [];
          var types = [];          
          $.each(queued, function(index, elem){
              names.push($(elem.previewElement).find("input[name=name]").val());
              types.push($(elem.previewElement).find("select[name=type]").val());
          })  
          $.ajax({
              type: 'POST',
              data: { _token: "{{ csrf_token() }}", name: names, type: types},            
              url: "{{ route('document.store') }}",
              success: function(response){                  
                  if (response.errors){                    
                    $.each(response.errors, function(index, elem){                      
                        var key = Object.keys(elem);    
                        console.log(elem);
                        $(key).each(function(idx, el){          
                            var key2 = el.split(".");
                            var input = $(queued[index].previewElement).find("input[name='" + key2[0] +"']");
                            $(input).addClass("is-invalid");            
                            $(input).next(".invalid-feedback").html(elem[key][0]);                                                    
                        })
                    })   
                  }
                  else {                      
                      myDropzone.processQueue();
                  }                
              }         
          })        
        }
    })
    $("body").on("input", "form input,select,.choices", function(){
        $(this).removeClass("is-invalid");        
        $(this).next(".invalid-feedback").html("");
    });
    $("body").on("input", "input,select,.choices", function(){
        $(this).removeClass("is-invalid");        
        $(this).next(".invalid-feedback").html("");
    });
    $("body").on("change", ".choices", function(){
        $(this).removeClass("is-invalid");        
        $(this).next(".invalid-feedback").html("");
    })     	
    $("#browse").on("click", function(){
        $("#previews").get(0).dropzone.hiddenFileInput.click();
    })
  });
</script>
<script type="text/template" id="templateupload">
@include('documents.uploader')
</script>
<script>
    var previewTemplate = $("#templateupload").html();
    var myDropzone = new Dropzone("#previews", { 
          url: "{{ route('document.upload') }}",
	        maxFiles: 10,
            maxFilesize: 5,
            autoProcessQueue: false,
            previewTemplate: previewTemplate,
            uploadMultiple: true,
            parallelUploads: 5,
            dictResponseError: 'Server not Configured',
            acceptedFiles: ".jpg,.jpeg,.gif,.png,.pdf",
            init:function(){	                 
                var self = this;                
                self.on("sendingmultiple", function (files, xhr, formData) {                      
                    $.each(files, function(index,elem){
                        formData.append('name[' +index +']', $(elem.previewElement).find("input[name='name']").val());
                        formData.append('type[' +index +']', $(elem.previewElement).find("select[name='type']").val());
                    })                
                    formData.append("_token", "{{ csrf_token() }}");	                    
                });
                self.on("addedfile", function(file){
                    $("#upload").removeClass("disabled");
                    $('#previews').prepend($(file.previewElement));
                });
                self.on("removedfile", function(file){
                    if (myDropzone.getQueuedFiles().length == 0){
                        $("#upload").addClass("disabled");
                    }
                });               
                self.on("completemultiple", function (files) {  
                    $.each(files, function(index,elem){
                        if (elem.status == Dropzone.ERROR){
                            elem.status = Dropzone.QUEUED;
                        }
                        else if(elem.status == Dropzone.SUCCESS){
                            $(elem.previewElement).find("input").prop("readonly", true);                            
                            $(elem.previewElement).find("select").prop("disabled", true);
                            $(elem.previewElement).find(".dz-success-mark").html("Upload berhasil");
                            $(elem.previewElement).find(".my-remove-button").hide();
                        }
                    })
                });		
            }
    });
</script>
@endpush


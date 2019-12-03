<input type="hidden" name="_token" value="{{ csrf_token() }}">
<div class="form-group row">
    <input type="hidden" name="folder_id" value="{{ $data->FOLDER_ID }}">
    <input type="hidden" name="parent" value="{{ $data->PARENT_ID }}">
    <input type="hidden" name="action" value="{{ $action }}">
    <label class="col-form-label col-md-3">Nama Folder</label>
    <div class="col-md-9">
        <input type="text" class="form-control" name="name" id="name" value="{{ $data->FOLDER_NAME }}">
        <div class="invalid-feedback">                
        </div>
    </div>
</div>
<script>
    @if($action == 'add')
    $("#form").attr("action", "{{ route('folder.store') }}");
    @elseif ($action == 'edit')
    $("#form").attr("action", "{{ route('folder.update',['id' => $data->FOLDER_ID]) }}");
    @endif
</script>

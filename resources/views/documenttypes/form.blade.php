<input type="hidden" name="_token" value="{{ csrf_token() }}">
<div class="form-group row">
    <input type="hidden" name="tipe_id" value="{{ $data->DOCUMENTTYPE_ID }}">
    <input type="hidden" name="action" value="{{ $action }}">
    <label class="col-form-label col-md-3">Tipe Dokumen</label>
    <div class="col-md-9">
        <input type="text" class="form-control" name="tipe" id="tipe" value="{{ $data->DOCUMENT_TYPE }}">
        <div class="invalid-feedback">                
        </div>
    </div>
</div>
@if ($action != 'add')
<div class="form-group row">
    <label class="col-form-label col-md-3">Aktif ?</label>
    <div class="col-md-9">            
        <div class="form-check mt-2">
            <input type="checkbox" value="Y" name="aktif" class="form-check-input" id="aktif"{{ $data->AKTIF == "Y" ? " checked" : "" }}>
            <label class="form-check-label" for="aktif">Ya</label>
        </div>
    </div>
</div>
@endif
<script>
    @if($action == 'add')
    $("#form").attr("action", "{{ route('documenttype.store') }}");
    @elseif ($action == 'edit')
    $("#form").attr("action", "{{ route('documenttype.update',['id' => $data->DOCUMENTTYPE_ID]) }}");
    @endif
</script>

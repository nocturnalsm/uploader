<input type="hidden" name="_token" value="{{ csrf_token() }}">
<input type="hidden" name="action" value="edit">
<div class="form-group row">    
    <label class="col-form-label col-md-3">Nama Dokumen</label>
    <input type="hidden" name="parent" id="parent" value="{{ $data->FOLDER_ID }}">
    <div class="col-md-9">
        <input type="text" class="form-control" name="name" id="name" value="{{ $data->DOCUMENT_NAME }}">        
        <div class="invalid-feedback">                
        </div>
    </div>
</div>
@hasrole('Super Admin')
<div class="form-group row">
    <label class="col-form-label col-md-3">User</label>
    <div class="col-md-9">
        <select name="user" id="user" class="form-control">
            <option value=""></option>
            @foreach ($users as $user)
            <option value="{{ $user->id }}"{{ $data->USER_ID == $user->id ? ' selected' : '' }}>
                {{ $user->name }}
            </option>
            @endforeach
        </select>
        <div class="invalid-feedback">                
        </div>
    </div>
</div>
@endhasrole
<script>
    $("#form").attr("action", "{{ route('document.update',['id' => $data->DOCUMENT_ID]) }}");
</script>
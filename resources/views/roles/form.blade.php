<input type="hidden" name="_token" value="{{ csrf_token() }}">
<div class="form-group row">
    <input type="hidden" name="role_id" id="role_id" value="{{ $data->id }}">
    <input type="hidden" name="action" value="{{ $action }}">
    <label class="col-form-label col-md-3">Nama Group User</label>
    <div class="col-md-9">
        <input type="text" class="form-control" name="name" id="name" value="{{ $data->name }}">
        <div class="invalid-feedback">                
        </div>
    </div>
</div>
<div class="form-group row">
    <label class="col-form-label col-md-3">Hak Akses</label>
    <div class="col-md-9">
        <div class="form-check mt-2">
            <input type="checkbox" value="Y" id="checkall" name="checkall" class="form-check-input">
            <label class="form-check-label" for="checkall">Centang semua</label>
        </div>
    </div>
    <div class="col-md-12">
        <div class="row">
            @foreach($permissions as $perm)
            <div class="col-3">
                <div class="form-check mt-2">
                    <input type="checkbox" value="Y" name="perm[{{ $perm->id }}]" class="check-permission form-check-input" {{ in_array($perm->id, $userPerm) ? " checked" : "" }}>
                    <label class="form-check-label" for="perm">{{ $perm->name }}</label>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
<script>
    @if($action == 'add')
    $("#form").attr("action", "{{ route('roles.store') }}");
    @elseif ($action == 'edit')
    $("#form").attr("action", "{{ route('roles.update',['id' => $data->id]) }}");
    @endif
</script>

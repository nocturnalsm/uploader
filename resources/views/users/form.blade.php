<input type="hidden" name="_token" value="{{ csrf_token() }}">
<div class="form-group row">
    <input type="hidden" name="user_id" id="user_id" value="{{ $data->id }}">
    <input type="hidden" name="action" value="{{ $action }}">
    <label class="col-form-label col-md-3">Nama</label>
    <div class="col-md-9">
        <input type="text" class="form-control" name="name" id="name" value="{{ $data->name }}">
        <div class="invalid-feedback">                
        </div>
    </div>
</div>
<div class="form-group row">
    <label class="col-form-label col-md-3">Username</label>
    <div class="col-md-9">
        <input type="text" class="form-control" name="username" id="username" value="{{ $data->username }}">
        <div class="invalid-feedback">                
        </div>
    </div>
</div>
<div class="form-group row">
    <label class="col-form-label col-md-3">Email</label>
    <div class="col-md-9">
        <input type="email" class="form-control" name="email" id="email" value="{{ $data->email }}">
        <div class="invalid-feedback">                
        </div>
    </div>
</div>
<div class="form-group row">
    <label class="col-form-label col-md-3">Password</label>
    <div class="col-md-9">
        <input type="password" class="form-control" name="password" id="password" value="">
        <div class="invalid-feedback">                
        </div>
    </div>
</div>
<div class="form-group row">
    <label class="col-form-label col-md-3">Konfirmasi Password</label>
    <div class="col-md-9">
        <input type="password" class="form-control" name="confirm" id="confirm" value="">
        <div class="invalid-feedback">                
        </div>
    </div>
</div>
<div class="form-group row">
    <label class="col-form-label col-md-3">Perusahaan</label>
    <div class="col-md-9">
        <select name="companies[]" id="companies" multiple="multiple" class="form-control">
            <option value=""></option>
            @foreach ($companies as $comp)
            <option value="{{ $comp->COMPANY_ID }}"{{ in_array($comp->COMPANY_ID, $userCompanies) ? ' selected' : '' }}>
                {{ $comp->NAME }}
            </option>
            @endforeach
        </select>
        <div class="invalid-feedback">                
        </div>
    </div>
</div>
<div class="form-group row">
    <label class="col-form-label col-md-3">Group User</label>
    <div class="col-md-9">
        <select name="roles[]" id="roles" multiple="multiple" class="form-control">
            <option value=""></option>
            @foreach ($roles as $role)
            <option value="{{ $role->name }}"{{ in_array($role->name, $userRoles) ? ' selected' : '' }}>
                {{ $role->name }}
            </option>
            @endforeach
        </select>        
        <div class="invalid-feedback">                
        </div>
    </div>
</div>

@if ($action != 'add')
<div class="form-group row">
    <label class="col-form-label col-md-3">Aktif ?</label>
    <div class="col-md-9">            
        <div class="form-check mt-2">
            <input type="checkbox" value="Y" name="aktif" class="form-check-input" id="aktif"{{ $data->aktif == "Y" ? " checked" : "" }}>
            <label class="form-check-label" for="aktif">Ya</label>
        </div>
    </div>
</div>
@endif

<script>
    var choices_company = new Choices("#companies", {removeItemButton: true});
    var choices_roles = new Choices("#roles", {removeItemButton: true});
    @if($action == 'add')
    $("#form").attr("action", "{{ route('users.store') }}");
    @elseif ($action == 'edit')
    $("#form").attr("action", "{{ route('users.update',['id' => $data->id]) }}");
    @endif
</script>
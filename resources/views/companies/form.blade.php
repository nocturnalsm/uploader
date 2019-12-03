<input type="hidden" name="_token" value="{{ csrf_token() }}">
<input type="hidden" name="company_id" id="company_id" value="{{ $data->COMPANY_ID }}">
<input type="hidden" name="action" value="{{ $action }}">
<style>

</style>
<div class="row">
    <div class="col-md-3 col-sm-12">
        <ul class="nav nav-pills flex-md-column">
            <li class="nav-item">                        
                <a class="nav-link active rounded-0" href="#pill-profil" data-toggle="pill">
                    <i class="fa fa-exclamation-circle d-none"></i>
                    Profil
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="#pill-pajak" data-toggle="pill">Data Pajak</a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="#pill-direktur" data-toggle="pill">Direktur & Komisaris</a>
            </li>
        </ul>
    </div>
    <div class="col-md-9 col-sm-12">
        <div class="tab-content pb-2">
            <div class="tab-pane fade show active" id="pill-profil">  
                <div class="form-group row">
                    <label class="col-form-label col-form-label-sm col-md-3">Jenis Klien</label>
                    <div class="col-md-9">
                        <div class="form-check-inline">
                            <label class="form-check-label col-form-label-sm">
                                <input value="B" {{ $data->COMPANY_TYPE == "B" ? " checked " : "" }} type="radio" class="form-check-input" name="jenis">Badan
                            </label>
                        </div>
                        <div class="form-check-inline">
                            <label class="form-check-label col-form-label-sm">
                                <input value="P" {{ $data->COMPANY_TYPE == "P" ? " checked " : "" }} type="radio" class="form-check-input" name="jenis">Pribadi
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-form-label-sm col-md-3">Nama</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control form-control-sm" name="nama" id="nama" value="{{ $data->NAME }}">
                        <div class="invalid-feedback">                
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-form-label-sm col-md-3">NPWP</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control form-control-sm input_npwp" name="npwp" id="npwp" value="{{ $data->NPWP }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-form-label-sm col-md-3">Alamat</label>
                    <div class="col-md-9">
                        <textarea rows="3" class="form-control form-control-sm" name="alamat" id="alamat">{{ $data->ALAMAT }}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-form-label-sm col-md-3">Kota</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control form-control-sm" name="kota" id="kota" value="{{ $data->KOTA }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-form-label-sm col-md-3">Telepon</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control form-control-sm" name="telepon" id="telepon" value="{{ $data->TELEPON }}">
                    </div>
                </div>
                @if ($action != 'add')
                <div class="form-group row">
                    <label class="col-form-label col-md-3 col-form-label-sm">Aktif ?</label>
                    <div class="col-md-9">            
                        <div class="form-check">
                            <input type="checkbox" value="Y" name="aktif" class="form-check-input form-control-sm mt-0" id="aktif"{{ $data->AKTIF == "Y" ? " checked" : "" }}>
                            <label class="form-check-label col-form-label-sm" for="aktif">Ya</label>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="tab-pane fade" id="pill-pajak">  
                <div class="form-group row">
                    <label class="col-form-label col-form-label-sm col-md-4">Email PKP</label>
                    <div class="col-md-8">
                        <input type="email" class="form-control form-control-sm" name="email_pkp" id="email_pkp" value="{{ $data->EMAIL_PKP }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-form-label-sm col-md-4">Pass Phrase</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control form-control-sm" name="passphrase" id="passphrase" value="{{ $data->PASSPHRASE }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-form-label-sm col-md-4">Username E-Faktur</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control form-control-sm" name="username_efaktur" id="username_efaktur" value="{{ $data->USERNAME_EFAKTUR }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-form-label-sm col-md-4">Password E-Faktur</label>
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="password" class="form-control form-control-sm" name="password_efaktur" id="password_efaktur" value="****** ">
                            <div class="input-group-append">
                                <button class="btn btn-success btn-sm showpass" type="button">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-form-label-sm col-md-4">Password Upload</label>
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="password" class="form-control form-control-sm" name="password_upload" id="password_upload" value="******">
                            <div class="input-group-append">
                                <button class="btn btn-success btn-sm showpass" type="button">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-form-label-sm col-md-4">EFIN</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control form-control-sm" name="efin" id="efin" value="{{ $data->EFIN }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-form-label-sm col-md-4">Email DJP</label>
                    <div class="col-md-8">
                        <input type="email" class="form-control form-control-sm" name="email_djp" id="email_djp" value="{{ $data->EMAIL_DJP }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-form-label-sm col-md-4">Password DJP</label>
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="password" class="form-control form-control-sm" name="password_djp" id="password_djp" value="******">
                            <div class="input-group-append">
                                <button class="btn btn-success btn-sm showpass" type="button">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>               
            </div>
            <div class="tab-pane fade" id="pill-direktur">  
                <h6>Direktur Utama</h6>
                <div class="form-group row{{ $data->COMPANY_TYPE != "B" ? " d-none" : ""}}">
                    <label class="col-form-label col-form-label-sm col-md-1 col-sm-12">Nama</label>
                    <div class="col-md-5 col-sm-12">
                        <input type="text" class="form-control form-control-sm" name="nama_direktur_utama" id="nama_direktur_utama" value="{{ $data->DIREKTUR_UTAMA }}">
                    </div>                    
                    <label class="col-form-label col-form-label-sm col-md-1 col-sm-12">NPWP</label>
                    <div class="col-md-5 col-sm-12">
                        <input type="text" class="form-control form-control-sm input_npwp" name="npwp_direktur_utama" id="npwp_direktur_utama" value="{{ $data->NPWP_DIREKTUR_UTAMA }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-12" id="datadirektur">
                        <h6>Direktur&nbsp;
                        <button type="button" class="btn btn-primary btn-sm" id="add_direktur">Tambah Direktur</button>
                        </h6>
                        <input type="hidden" name="deletedir">
                        @if(isset($direktur))
                        @foreach($direktur as $dir)
                        <div class="direktur col-md-12 pl-0">
                            <input type="hidden" name="direktur_id[]" value="{{ $dir->DIREKTUR_ID }}">                            
                            <div class="form-group row mb-1">
                                <label class="col-form-label col-form-label-sm col-md-3 col-sm-12">Nama</label>
                                <input name="direktur_nama[]" value="{{ $dir->NAMA }}" type="text" class="input_direktur col-md-9  col-sm-12 form-control form-control-sm">
                            </div>
                            <div class="form-group row mb-1">
                                <label class="col-form-label col-form-label-sm col-md-3 col-sm-12">NPWP</label>
                                <input name="direktur_npwp[]" value="{{ $dir->NPWP }}" type="text" class="input_npwp form-control col-md-9  col-sm-12 form-control-sm">
                            </div>
                            <div class="form-group row mb-1 pl-2 pb-2 small">
                                <a href="#" class="hapus_direktur">
                                <i class="fa fa-trash text-danger"></i>&nbsp;Hapus
                                </a>
                            </div>
                        </div>
                        @endforeach
                        @endif
                    </div>
                    <div class="col-md-6 col-sm-12" id="datakomisaris">
                        <h6>Komisaris&nbsp;
                        <button type="button" class="btn btn-primary btn-sm" id="add_komisaris">Tambah Komisaris</button>
                        </h6>                        
                        <input type="hidden" name="deletekom">
                        @if(isset($komisaris))
                        @foreach($komisaris as $kom)                        
                        <div class="komisaris col-md-12 pl-0">
                            <input type="hidden" name="komisaris_id[]" value="{{ $kom->KOMISARIS_ID }}">
                            <div class="form-group row mb-1">
                                <label class="col-form-label col-form-label-sm col-md-3 col-sm-12">Nama</label>
                                <input type="text" name="komisaris_nama[]" value="{{ $kom->NAMA }}" class="input_komisaris col-md-9 col-sm-12 form-control form-control-sm">
                            </div>
                            <div class="form-group row mb-1">
                                <label class="col-form-label col-form-label-sm col-md-3 col-sm-12">NPWP</label>
                                <input type="text" name="komisaris_npwp[]" value="{{ $kom->NPWP }}" class="input_npwp form-control col-md-9 col-sm-12 form-control-sm">
                            </div>
                            <div class="form-group row mb-1 pl-2 pb-2 small">
                                <a href="#" class="hapus_komisaris">
                                <i class="fa fa-trash text-danger"></i>&nbsp;Hapus
                                </a>
                            </div>
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/template" id="template_direktur">
    <div class="direktur col-md-12 pl-0">
        <input type="hidden" name="direktur_id[]" value="">
        <div class="form-group row mb-1">
            <label class="col-form-label col-form-label-sm col-md-3 col-sm-12">Nama</label>
            <input name="direktur_nama[]" type="text" class="input_direktur col-md-9  col-sm-12 form-control form-control-sm">
        </div>
        <div class="form-group row mb-1">
            <label class="col-form-label col-form-label-sm col-md-3 col-sm-12">NPWP</label>
            <input name="direktur_npwp[]" type="text" class="input_npwp form-control col-md-9  col-sm-12 form-control-sm">
        </div>
        <div class="form-group row mb-1 pl-2 pb-2 small">
            <a href="#" class="hapus_direktur">
            <i class="fa fa-trash text-danger"></i>&nbsp;Hapus
            </a>
        </div>
    </div>
</script>
<script type="text/template" id="template_komisaris">
    <div class="komisaris col-md-12 pl-0">
        <input type="hidden" name="komisaris_id[]" value="">
        <div class="komisaris col-md-12 pl-0">
            <div class="form-group row mb-1">
                <label class="col-form-label col-form-label-sm col-md-3 col-sm-12">Nama</label>
                <input type="text" name="komisaris_nama[]" class="input_komisaris col-md-9  col-sm-12 form-control form-control-sm">
            </div>
            <div class="form-group row mb-1">
                <label class="col-form-label col-form-label-sm col-md-3 col-sm-12">NPWP</label>
                <input type="text" name="komisaris_npwp[]" class="input_npwp form-control  col-sm-12 col-md-9 form-control-sm">
            </div>
            <div class="form-group row mb-1 pl-2 pb-2 small">
                <a href="#" class="hapus_komisaris">
                <i class="fa fa-trash text-danger"></i>&nbsp;Hapus
                </a>
            </div>
        </div>
    </div>
</script>
<script>
    $(function(){
        @if($action == 'add')
        $("#form").attr("action", "{{ route('company.store') }}");
        @elseif ($action == 'edit')
        $("#form").attr("action", "{{ route('company.update',['id' => $data->COMPANY_ID]) }}");
        @endif
        $(".input_npwp").inputmask("99.999.999.9-999.999");
        $("button.showpass").on("click", function(){
            var i = $(this).find("i");
            var input = $(this).closest("div").prev("input");
            if ($(i).hasClass("fa-eye")){                
                $.post("{{ route('company.showpass') }}", {_token: "{{ csrf_token() }}", id: "{{ $data->COMPANY_ID }}", field: $(input).attr('name')}, function(msg){
                    if (msg.pass || msg.pass == ""){
                        $(input).val(msg.pass);
                        $(input).attr("type","text");
                        $(i).attr("class", "fa fa-eye-slash");
                    }
                });
                
            }
            else {
                $(i).attr("class", "fas fa-eye");
                $(input).val("******");
                $(input).attr("type","password");
            }
        })
    })
</script>
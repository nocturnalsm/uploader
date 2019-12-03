<?php 

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Companies;
use App\Folder;
use Validator, Auth, DB;

class CompanyController extends Controller {

	public function __construct()
	{				
		$this->middleware(['permission:company.list']);		
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		$columns = ["NAME","KOTA"];
		if ($request->ajax()){			
			$data = Companies::select("COMPANY_ID", "COMPANY_TYPE", "NAME","KOTA", "AKTIF", DB::raw("'' As ACTION"));
			$totalData = $data->count();
    		$totalFiltered = $totalData;
			if (!empty($request->search["value"])){
				foreach($columns as $key=>$col){
					if ($key == 0){
						$data->where($col, "LIKE", '%' .$request->search['value'] .'%');
					}
					else {
						$data->orWhere($col, "LIKE", '%' .$request->search['value'] .'%');
					}					
				}
				$totalFiltered = $data->count();
			}
			$data->orderBy($columns[$request->order[0]['column']], $request->order[0]['dir'])
				 ->skip($request->start)
				 ->take($request->length);
			$data = $data->get()->each(function($item, $key){
				if ($item->COMPANY_TYPE == "B"){
					$item->COMPANY_TYPE = "Badan";
				}
				else if ($item->COMPANY_TYPE == "P"){
					$item->COMPANY_TYPE = "Pribadi";
				}
				$item->ACTION = $this->buttons($item->COMPANY_ID);
				return $item;
			});
			return ["draw" => intval($request->draw), "recordsTotal" => $totalData,
					"recordsFiltered" => $totalFiltered,
					"data" => $data
				   ];
		}
		else {
			/*
			set_time_limit(0);
			Companies::where("COMPANY_ID","<>",28)->where("COMPANY_ID", "=", 15)->get()->each(function($row){
				$data = DB::table("folders")->where("COMPANY_ID", 28)->where("PARENT_ID",0)->get();
				$data->each(function($folder) use ($row){
 					$newFolder = new Folder();
					$newFolder->COMPANY_ID = $row->COMPANY_ID;
					$newFolder->FOLDER_NAME = $folder->FOLDER_NAME;
					$newFolder->PARENT_ID = 0;
					$newFolder->save();
					Folder::copy($folder->FOLDER_ID, $newFolder->FOLDER_ID);
				});
			});
			*/
			return view ("companies.index");
		}
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		if (auth()->user()->can("company.create")){
			$data = new Companies;
			$data->COMPANY_TYPE = "B";
			return response()->view('companies.form', ["data" => $data,
										   "action" => 'add']);
		}
		else {			
			return response("", 403);
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		if (auth()->user()->cannot("company.create")){
			return response(403);
		}	
		$v = Validator::make($request->all(), [
			'nama' => 'required|unique:companies,NAME'
		],
		["nama.required" => "Nama perusahaan harus diisi",
		 "nama.unique" => "Nama perusahaan sudah ada"]);
	
		if ($v->fails())
		{
			return response()->json(["errors" => $v->errors()]);
		}
		
		try {
			DB::beginTransaction();
			$data = new Companies;			
			$data->COMPANY_TYPE = $request->jenis;
			$data->NAME = $request->nama;		
			$data->NPWP = $request->npwp;
			$data->ALAMAT = $request->alamat;
			$data->KOTA = $request->kota;
			$data->TELEPON = $request->telepon;
			$data->EMAIL_PKP = $request->email_pkp;
			$data->PASSPHRASE = $request->passphrase;
			$data->USERNAME_EFAKTUR = $request->username_efaktur;
			$data->PASSWORD_EFAKTUR = $request->password_efaktur;
			$data->PASSWORD_UPLOAD = $request->password_upload;
			$data->EFIN = $request->efin;
			$data->EMAIL_DJP = $request->email_djp;
			$data->PASSWORD_DJP = $request->password_djp;
			if ($request->jenis == "B"){
				$data->DIREKTUR_UTAMA = $request->nama_direktur_utama;
				$data->NPWP_DIREKTUR_UTAMA = $request->npwp_direktur_utama;
			}
			else {
				$data->DIREKTUR_UTAMA = "";
				$data->NPWP_DIREKTUR_UTAMA = "";
			}
			$data->save();
			$direktur = $request->direktur_nama;
			$datadir = Array();
			if (is_array($direktur) && count($direktur) > 0){
				foreach($direktur as $key=>$dir){
					$datadir[] = ["NAMA" => $dir,
								  "NPWP" => $request->direktur_npwp[$key],
								  "COMPANY_ID" => $data->COMPANY_ID];
				}
				DB::table("direktur")->insert($datadir);
			}
			$komisaris = $request->komisaris_nama;
			$datakom = Array();
			if (is_array($komisaris) && count($komisaris) > 0){
				foreach($komisaris as $key=>$kom){
					$datakom[] = ["NAMA" => $kom,
								  "NPWP" => $request->komisaris_npwp[$key],
								  "COMPANY_ID" => $data->COMPANY_ID];
				}
				DB::table("komisaris")->insert($datakom);
			}
			
			DB::commit();
			return response()->view("partials.flash", ["type" => "success", "text" => "Penyimpanan Berhasil"]);
		}
		catch (Exception $e){
			DB::rollBack();
			return response()->view("partials.flash", ["type" => "warning", "text" => "Penyimpanan data gagal.<br>" .$e->getMessage()]);
		}
	}

	/**
	 * Update the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Request $request)
	{
		if (auth()->user()->cannot("company.edit")){
			return response(403);
		}	
		$v = Validator::make($request->all(), [
			'nama' => 'required|unique:companies,NAME,' .$request->company_id .",COMPANY_ID"],
		["nama.required" => "Nama perusahaan harus diisi",
		 "nama.unique" => "Nama perusahaan sudah ada"]);
	
		if ($v->fails())
		{
			return response()->json(["errors" => $v->errors()]);
		}
		
		try {
			DB::beginTransaction();
			$data = Companies::where("COMPANY_ID", $request->company_id)->first();								
			$data->NAME = $request->nama;		
			$data->COMPANY_TYPE = $request->jenis;
			$data->NAME = $request->nama;		
			$data->NPWP = $request->npwp;
			$data->ALAMAT = $request->alamat;
			$data->KOTA = $request->kota;
			$data->TELEPON = $request->telepon;
			$data->EMAIL_PKP = $request->email_pkp;
			$data->PASSPHRASE = $request->passphrase;
			$data->USERNAME_EFAKTUR = $request->username_efaktur;
			$data->PASSWORD_EFAKTUR = $request->password_efaktur;
			$data->PASSWORD_UPLOAD = $request->password_upload;
			$data->EFIN = $request->efin;
			$data->EMAIL_DJP = $request->email_djp;
			$data->PASSWORD_DJP = $request->password_djp;
			if ($request->jenis == "B"){
				$data->DIREKTUR_UTAMA = $request->nama_direktur_utama;
				$data->NPWP_DIREKTUR_UTAMA = $request->npwp_direktur_utama;
			}
			else {
				$data->DIREKTUR_UTAMA = "";
				$data->NPWP_DIREKTUR_UTAMA = "";
			}
			$data->aktif = $request->aktif == "Y" ? $request->aktif : "T";
			$direktur = $request->direktur_nama;			
			if (is_array($direktur) && count($direktur) > 0){
				foreach($direktur as $key=>$dir){
					if ($request->direktur_id[$key] == ""){
						DB::table("direktur")->insert([
							"NAMA" => $dir,
							"NPWP" => $request->direktur_npwp[$key],
							"COMPANY_ID" => $request->company_id
						]);
					}
					else {
						DB::table("direktur")->where("DIREKTUR_ID", $request->direktur_id[$key])
											 ->update(["NAMA" => $dir,
											 "NPWP" => $request->direktur_npwp[$key]]);
					}
				}				
			}			
			$deletedir = explode(",", $request->deletedir);
			DB::table("direktur")->whereIn("DIREKTUR_ID", $deletedir)->delete();
			$komisaris = $request->komisaris_nama;
			if (is_array($komisaris) && count($komisaris) > 0){
				foreach($komisaris as $key=>$kom){
					if ($request->komisaris_id[$key] == ""){
						DB::table("komisaris")->insert([
							"NAMA" => $kom,
							"NPWP" => $request->komisaris_npwp[$key],
							"COMPANY_ID" => $request->company_id
						]);
					}
					else {
						DB::table("komisaris")->where("KOMISARIS_ID", $request->komisaris_id[$key])
											 ->update(["NAMA" => $kom,
											 "NPWP" => $request->komisaris_npwp[$key]]);
					}
				}
			}
			$deletekom = explode(",", $request->deletekom);
			DB::table("komisaris")->whereIn("KOMISARIS_ID", $deletekom)->delete();
			DB::commit();
			$data->save();
			return response()->view("partials.flash", ["type" => "success", "text" => "Penyimpanan Berhasil"]);
		}
		catch (Exception $e){
			DB::rollBack();
			return response()->view("partials.flash", ["type" => "warning", "text" => "Penyimpanan data gagal.<br>" .$e->getMessage()]);
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		if (auth()->user()->cannot('company.edit')){
			return response("", 403);
		}
		$data = Companies::where("COMPANY_ID", $id)->first();
		$direktur = DB::table("direktur")->where("COMPANY_ID", $id)->orderBy("NAMA")->get();
		$komisaris = DB::table("komisaris")->where("COMPANY_ID", $id)->orderBy("NAMA")->get();
		return response()->view('companies.form', ["data" => $data, 
								"direktur" => $direktur, "komisaris" => $komisaris,
								"action" => 'edit']);
	}	

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Request $request)
	{
		if (auth()->user()->cannot('company.delete')){
			$response = view("partials.flash", ["type" => "error", "text" => "Anda tidak memiliki hak akses"]);
			return response($response,500);
		}
		try {
			$data = Companies::where("COMPANY_ID", $request->id)->delete();
			return response()->view("partials.flash", ["type" => "info", "text" => "Data berhasil dihapus"]);
		}
		catch (Exception $e){
			$response = view("partials.flash", ["type" => "error", "text" => "Penghapusan data gagal"]);
			return response($response, 500)
							->header('Content-Type', 'text/html');
		}
	}
	private function buttons($id)
	{
		$buttons = "";                                        
		if (auth()->user()->can('company.edit')){                    
			$buttons .= '<button class="btn btn-sm btn-primary btn-edit" data-id="' .$id
					    .'"> <i class="fa fa-edit"></i>&nbsp;Edit</button>&nbsp;&nbsp;';
		}
		if (auth()->user()->can('company.delete')){
			$buttons .= '<button class="btn btn-sm btn-danger btn-delete" data-id="' .$id
						.'"> <i class="fa fa-trash"></i>&nbsp;Hapus</button>&nbsp;&nbsp;';
		}
		return $buttons;
	}
	public function getPassword(Request $request)
	{
		if (auth()->user()->cannot("company.edit")){
			return response(403);
		}
		else {
			$id = $request->id;
			$field = strtoupper($request->field);
			if ($id != "" && $field != ""){
				$data = Companies::where("COMPANY_ID", $id)->value($field);
				$data = $data ? $data : "";
				return response()->json(["pass" => $data]);				
			}
		}
	}
}

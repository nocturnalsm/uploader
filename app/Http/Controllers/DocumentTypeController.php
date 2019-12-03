<?php 

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\DocumentTypes;
use Validator, DB, Auth;

class DocumentTypeController extends Controller {

	public function __construct()
	{				
		$this->middleware('permission:documenttype.list');		
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		$columns = ["FOLDER_ID","AKTIF"];
		if ($request->ajax()){
			$data = DocumentTypes::select("FOLDER_ID", "FOLDER_ID",
									      "AKTIF", DB::raw("'' As ACTION"));
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
					$item->ACTION = $this->buttons($item->FOLDER_ID);
					return $item;
			});
			return ["draw" => intval($request->draw), "recordsTotal" => $totalData,
					"recordsFiltered" => $totalFiltered,
					"data" => $data 
				   ];
		}
		else {
			return view ("documenttypes.index");
		}
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		if (!Auth::user()->can('documenttype.create')){			
			return response("",403);
		}
		$data = new DocumentTypes;
		$data->AKTIF = 'Y';
		return view('documenttypes.form', ["data" => $data,
										   "action" => "add"]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		if (!Auth::user()->can("documenttype.create")){
			return response(403);
		}
	  	$v = Validator::make($request->all(), [
			'tipe' => 'required|unique:folders,FOLDER_ID',
		],
		["tipe.required" => "Nama tipe dokumen harus diisi",
		 "tipe.unique" => "Nama tipe dokumen sudah ada"]);
	
		if ($v->fails())
		{
			return response()->json(["errors" => $v->errors()]);
		}
		try {
			$data = new DocumentTypes;
			$data->AKTIF = "Y";
			$data->FOLDER_ID = $request->tipe;		
			$data->save();
			return response()->view("partials.flash", ["type" => "success", "text" => "Penyimpanan Berhasil"]);
		}
		catch (Exception $e){
			return response()->view("partials.flash", ["type" => "warning", "text" => "Penyimpanan data gagal.<br>" .$e->getMessage()]);
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		if (!Auth::user()->can('documenttype.edit')){
			return response($response,500);
		}
		$data = DocumentTypes::where("FOLDER_ID", $id)->first();
		return response()->view('documenttypes.form', ["data" => $data, 
										   "action" => "edit"]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Request $request)
	{
		if (!Auth::user()->can("documenttype.edit")){
			return response(403);
		}
	  	$v = Validator::make($request->all(), [
			'tipe' => 'required|unique:folders,FOLDER_ID,' .$request->tipe_id .",FOLDER_ID"
		],
		["tipe.required" => "Nama tipe dokumen harus diisi",
		 "tipe.unique" => "Nama tipe dokumen sudah ada"]);
	
		if ($v->fails())
		{
			return response()->json(["errors" => $v->errors()]);
		}
		try {
			$data = DocumentTypes::where("FOLDER_ID", $request->tipe_id)->first();					
			$data->AKTIF = $request->aktif == "Y" ? $request->aktif : "T";
			$data->FOLDER_ID = $request->tipe;		
			$data->save();
			return response()->view("partials.flash", ["type" => "success", "text" => "Penyimpanan Berhasil"]);
		}
		catch (Exception $e){
			return response()->view("partials.flash", ["type" => "warning", "text" => "Penyimpanan data gagal.<br>" .$e->getMessage()]);
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Request $request)
	{
		if (!Auth::user()->can('documenttype.delete')){
			$response = view("partials.flash", ["type" => "error", "text" => "Anda tidak memiliki hak akses"]);
			return response($response,500);
		}
		try {
			$data = DocumentTypes::where("FOLDER_ID", $request->id)->delete();
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
		if (auth()->user()->can('documenttype.edit')){                    
			$buttons .= '<button class="btn btn-sm btn-primary btn-edit" data-id="' .$id
					    .'"> <i class="fa fa-edit"></i>&nbsp;Edit</button>&nbsp;&nbsp;';
		}
		if (auth()->user()->can('documenttype.delete')){
			$buttons .= '<button class="btn btn-sm btn-danger btn-delete" data-id="' .$id
						.'"> <i class="fa fa-trash"></i>&nbsp;Hapus</button>&nbsp;&nbsp;';
		}
		return $buttons;
	}

}

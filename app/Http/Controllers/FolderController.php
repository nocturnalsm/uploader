<?php 

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Folder;
use Validator, DB, Auth;
use Illuminate\Validation\Rule;

class FolderController extends Controller {

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Request $request)
	{
		if (!Auth::user()->can('document.create')){			
			return response("",403);
		}
		$data = new Folder;
		$data->PARENT_ID = $request->parent;		
		return view('documents.folder', ["data" => $data, "action" => "add"]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$user = auth()->user();
		if (!$user->can("document.create")){
			return response(403);
		}
		$parent = $request->parent;
	  	$v = Validator::make($request->all(), [
			'name' => ['required',
					   Rule::unique('folders', 'FOLDER_NAME')->where(function($query) use ($parent){
							return $query->where("PARENT_ID", $parent);
					   })]
		],
		["name.required" => "Nama folder harus diisi",
		 "name.unique" => "Nama folder sudah ada"]);
	
		if ($v->fails())
		{
			return response()->json(["errors" => $v->errors()]);
		}
		try {
			$data = new Folder;			
			$data->FOLDER_NAME = $request->name;
			$data->PARENT_ID = $request->parent;
			$data->COMPANY_ID = $user->settings("current_company");			
			$data->save();
			return response()->view("partials.flash", ["type" => "success", "text" => "Penyimpanan Berhasil"]);
		}
		catch (Exception $e){
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
		if (!Auth::user()->can('document.edit')){
			return response($response,500);
		}
		$data = Folder::where("FOLDER_ID", $id)->first();
		return response()->view('documents.folder', ["data" => $data, 
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
		if (!Auth::user()->can("document.edit")){
			return response(403);
		}
		$parent = $request->parent;
	  	$v = Validator::make($request->all(), [
			'name' => ['required',
					   Rule::unique('folders', 'FOLDER_NAME')->where(function($query) use ($parent){
							return $query->where("PARENT_ID", $parent);
					   })->ignore($request->folder_id, 'FOLDER_ID')]
		],
		["name.required" => "Nama Folder harus diisi",
		 "name.unique" => "Nama Folder sudah ada"]);
	
		if ($v->fails())
		{
			return response()->json(["errors" => $v->errors()]);
		}
		try {
			$data = Folder::where("FOLDER_ID", $request->folder_id)->first();								
			$data->FOLDER_NAME= $request->name;		
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
		if (!Auth::user()->can('document.delete')){
			$response = view("partials.flash", ["type" => "error", "text" => "Anda tidak memiliki hak akses"]);
			return response($response,500);
		}
		try {
			$data = Folder::where("FOLDER_ID", $request->id)->delete();
			return response()->view("partials.flash", ["type" => "info", "text" => "Data berhasil dihapus"]);
		}
		catch (Exception $e){
			$response = view("partials.flash", ["type" => "error", "text" => "Penghapusan data gagal"]);
			return response($response, 500)
							->header('Content-Type', 'text/html');
		}
	}
	
}

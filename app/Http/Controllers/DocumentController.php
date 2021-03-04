<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Google_Client;
use Google_Http_Request;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use App\Folder;
use App\Documents;
use App\Companies;
use App\Settings;
use App\User;
use App\UserSettings;
use App\Notifications;
use App\Company;
use Validator, DB;
use Illuminate\Validation\Rule;

class DocumentController extends Controller {

	public function __construct()
	{
		$this->middleware('permission:document.list');
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		$columns = ["DOCUMENT_NAME","FOLDER_ID","TGL_UPLOAD"];
		$user = auth()->user();
		$currentCompany = $user->settings("current_company");
		$parent = $request->folder ? $request->folder : 0;
		if ($request->ajax()){
			if (!empty($request->search["value"])){
				$data = Documents::select("DOCUMENT_ID", "DOCUMENT_NAME", "documents.FOLDER_ID",
									DB::raw("DATE_FORMAT(documents.created_at, '%d %M %Y') AS TGL_UPLOAD"),
									DB::raw("'' AS ACTION"),"FILE_NAME","FILE_MIME",
									DB::raw("'document' AS TYPE"),
									DB::raw("users.name AS username"))
						->join("users","documents.USER_ID","=","users.id")
						->join("folders", "documents.FOLDER_ID","=","folders.FOLDER_ID")
						->where("folders.COMPANY_ID", $currentCompany)
						->where("DOCUMENT_NAME", "LIKE", "%" .$request->search["value"] ."%");
				$totalData = $data->count();
				$totalFiltered = $totalData;
			}
			else {
				$folder = DB::table("folders")
							->select("FOLDER_ID", "FOLDER_NAME", "PARENT_ID",
									DB::raw("DATE_FORMAT(created_at, '%d %M %Y') AS TGL_UPLOAD"),
									DB::raw("'' AS ACTION"),
									DB::raw("'' AS FILE_NAME"),
									DB::raw("'' AS FILE_MIME"),
									DB::raw("'folder' AS TYPE"),
									DB::raw("'' AS username"));
				if ($parent == 0){
					$folder->where("COMPANY_ID", $currentCompany)
						->where("PARENT_ID", 0);
				}
				else {
					$folder->where("PARENT_ID", $parent);
				}
				$data = Documents::select("DOCUMENT_ID", "DOCUMENT_NAME", "FOLDER_ID",
									DB::raw("DATE_FORMAT(documents.created_at, '%d %M %Y') AS TGL_UPLOAD"),
									DB::raw("'' AS ACTION"),"FILE_NAME","FILE_MIME",
									DB::raw("'document' AS TYPE"),
									DB::raw("users.name AS username"))
						->join("users","documents.USER_ID","=","users.id")
						->where("FOLDER_ID", $parent)
						->union($folder);
				$totalData = $data->count();
				$totalFiltered = $totalData;
			}
			$data->orderBy("TYPE", "DESC")
				 	  ->orderBy($columns[$request->order[0]['column']], $request->order[0]['dir']);
			$data = $data->get()->each(function($item, $key){
					$item->ACTION = $this->buttons($item->DOCUMENT_ID, $item->TYPE);
					if ($item->TYPE == "document"){
						$item->DOCUMENT_NAME = '<i class="fa fa-file"></i>&nbsp;&nbsp;'
											.'<a class="file-preview" data-id="' .$item->DOCUMENT_ID .'" data-type="'
											.$item->FILE_MIME .'" data-filename="' .$item->FILE_NAME
											.'" href="#">' .$item->DOCUMENT_NAME .'</a>';
					}
					else if ($item->TYPE == "folder"){
						$item->DOCUMENT_NAME =  '<i class="fa fa-folder"></i>&nbsp;&nbsp;'
												.'<a class="folder-preview" data-id="' .$item->DOCUMENT_ID .'" href="#">'
												.$item->DOCUMENT_NAME .'</a>';
					}
					return $item;
			});
			return ["draw" => intval($request->draw), "recordsTotal" => $totalData,
							"recordsFiltered" => $totalFiltered,
							"data" => $data
				   ];
		}
		else {
			return view ("documents.index");
		}
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
		$errors = Array();
		foreach ($request->name as $key=>$name){
			$v = Validator::make($request->all(), [
				'name.' .$key => 'required|unique:documents,DOCUMENT_NAME',
			],
			["name." .$key .".required" => "Nama dokumen harus diisi",
			 "name." .$key .".unique" => "Nama Dokumen sudah ada",
			]
			);
			if ($v->fails()){
				$errors[$key] = $v->errors();
			}
		}
		if (count($errors) > 0)	{
			return response()->json(["errors" => $errors]);
		}
	}
	public function upload(Request $request)
	{
		try {
			$user = auth()->user();
			$drive = $this->getDrive();
			if ($drive){
				$files = $request->file('file');
				$driveFolder = Settings::get("google_drive_upload_folder");
				$message = Array();
				foreach ($files as $file){
					$result = $this->saveFile($drive, $file, $driveFolder);
					if ($result){
						$doc = new Documents;
						$doc->FOLDER_ID = $request->folder;
						$doc->DOCUMENT_NAME = $result->name;
						$doc->FILE_ID = $result->id;
						$doc->FILE_NAME = $result->name;
						$doc->FILE_MIME = $result->mimeType;
						//$doc->DOCUMENT_NAME = "New Document";
						//$doc->FILE_ID = "X";
						//$doc->FILE_NAME = "New Document";
						//$doc->FILE_MIME = "text/html";
						$doc->USER_ID = $user->id;
						$doc->save();
						$notif = new Notifications;
						$notif->USER_ID = 1;
						$notif->NOTIFICATION = $user->name ." mengupload dokumen " .$result->name;
						//$notif->NOTIFICATION = $user->name ." mengupload dokumen baru";
						$notif->URL = "http://www.drive.google.com";
						$notif->save();
						$message[] = 0; // success
					}
					else {
						$message[] = 1;
					}
				}
				return response()->json(["message" => $message]);
			}
			else {
				throw new \Exception("Cannot connect to google drive");
			}
		}
		catch (\Throwable $e){
			return response($e->getMessage(), 500);
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
		if (!auth()->user()->can('document.edit')){
			$response = view("partials.flash", ["type" => "error", "text" => "Anda tidak memiliki hak akses"]);
			return response($response,500);
		}
		$data = Documents::where("DOCUMENT_ID", $id)->first();
		$users = User::select("id","name")->get();
		return view('documents.form', compact('data','users'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id, Request $request)
	{
		$user = auth()->user();
		if (!$user->can('document.edit')){
			$response = view("partials.flash", ["type" => "error", "text" => "Anda tidak memiliki hak akses"]);
			return response($response,500);
		}
		$folder = $request->parent;
		$v = Validator::make($request->all(), [
			'name' => ['required',
						Rule::unique('documents', 'DOCUMENT_NAME')->where(function($query) use ($folder){
							return $query->where("FOLDER_ID", $folder);
						})->ignore($id, 'DOCUMENT_ID')]
		],
			["name.required" => "Nama dokumen harus diisi",
			"name.unique" => "Nama Dokumen sudah ada",
			]
		);
		if ($v->fails())
		{
			return response()->json(["errors" => $v->errors()]);
		}
		try {
			$document = Documents::where("DOCUMENT_ID", $id)->first();
			$document->DOCUMENT_NAME = $request->name;
			if ($user->hasRole("Super Admin")){
				$document->USER_ID = $request->user;
			}
			$document->save();
			return view("partials.flash", ["type" => "success", "text" => "Update berhasil"]);
		}
		catch (Exception $e){
			return view("partials.flash", ["type" => "error", "text" => $e->getMessage()]);
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
		if (!auth()->user()->can('document.delete')){
			$response = view("partials.flash", ["type" => "error", "text" => "Anda tidak memiliki hak akses"]);
			return response($response,500);
		}
		try {
			$data = Documents::where("DOCUMENT_ID", $request->id)->delete();
			return view("partials.flash", ["type" => "info", "text" => "Dokumen berhasil dihapus"]);
		}
		catch (Exception $e){
			$response = view("partials.flash", ["type" => "error", "text" => "Penghapusan data gagal"]);
			return response($response, 500)
							->header('Content-Type', 'text/html');
		}
	}
	private function buttons($id, $type)
	{
		$buttons = "";
		if (auth()->user()->can('document.edit')){
			$buttons .= '<button class="btn btn-sm btn-primary btn-edit" data-id="' .$id
					    .'" data-type="' .$type .'"> <i class="fa fa-edit"></i>&nbsp;Edit</button>&nbsp;&nbsp;';
		}
		if (auth()->user()->can('document.delete')){
			$buttons .= '<button class="btn btn-sm btn-danger btn-delete" data-id="' .$id
						.'" data-type="' .$type .'"> <i class="fa fa-trash"></i>&nbsp;Hapus</button>&nbsp;&nbsp;';
		}
		return $buttons;
	}
	private function getDrive()
	{
		$settings = Settings::get(['google_drive_access_token','google_drive_client_id','google_drive_client_secret']);
		$token = $settings['google_drive_access_token'];
		$clientid = $settings['google_drive_client_id'];
		$clientsecret = $settings['google_drive_client_secret'];

		try {
			$client = new Google_Client();
			$client->setClientId($clientid);
			$client->setClientSecret($clientsecret);
			$client->setRedirectUri("https://developers.google.com/oauthplayground");
			$client->setScopes(array('https://www.googleapis.com/auth/drive'));
			$client->refreshToken($token);
			$tokens = $client->getAccessToken();
			$client->setAccessToken($tokens);

			$service = new Google_Service_Drive($client);

			return $service;
		}
		catch (\Throwable $e){
			return false;
		}
	}
	private function saveFile($service, $file, $folder)
	{
		$fileMetadata = new Google_Service_Drive_DriveFile(array(
		  'name' => $file->getClientOriginalName(),
		  'parents' => array($folder)
		));

		$response = $service->files->create($fileMetadata, array(
		  'data' => $file->get(),
		  'mimeType' => $file->getClientMimeType(),
		  'uploadType' => 'multipart'));
		return $response;
	}
	public function getFile(Request $request)
	{
		$id = $request->id;
		$file = Documents::where("DOCUMENT_ID", $id)->select("FILE_ID","FILE_MIME","FILE_NAME")->first();
		if ($file){
			$drive = $this->getDrive();
			$response = $drive->files->get($file->FILE_ID, array(
				'alt' => 'media'
			));
			if ($file->FILE_MIME == "application/pdf"){
				return response()->stream(function () use ($response){
					echo $response->getBody()->getContents();
				}, 200, [
					'Content-Type' => 'application/pdf',
					'Content-Disposition' => 'inline; filename="'.$file->FILE_NAME.'"'
				]);
			}
			else {
				return $response->getBody()->getContents();
			}
		}
	}
}

<?php 

namespace App\Http\Controllers;
use App\Documents;
use App\Companies;
use App\User;
use Auth;
use DB;


class HomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{		
		$user = Auth::user();						
		$currentCompany = $user->settings("current_company");
		if ($currentCompany == ""){
			$currentCompany = $user->companies()->first();
		}		
		if ($user->hasRole("Super Admin")){
			$lastUploads = Documents::select("DOCUMENT_ID","DOCUMENT_NAME",DB::raw("companies.NAME AS company_name"),
											 DB::raw("users.name AS username"), "FILE_NAME", "FILE_MIME", "FOLDER_NAME",
											 DB::raw("DATE_FORMAT(documents.created_at,'%d %M %Y %H:%i:%s') AS upload_date"))
									->join("folders","folders.FOLDER_ID","=","documents.FOLDER_ID")
									->join("companies", "folders.COMPANY_ID","=", "companies.COMPANY_ID")									
									->join("users", "users.id","=","documents.USER_ID")
									->orderBy("documents.created_at","DESC")->limit(10);			
		}
		else {
			$lastUploads = Documents::select("DOCUMENT_ID","DOCUMENT_NAME","FILE_NAME", "FILE_MIME",
											 "FOLDER_NAME", DB::raw("DATE_FORMAT(documents.created_at,'%d %M %Y %H:%i:%s') AS upload_date"))
									->join("folders","folders.FOLDER_ID","=","documents.FOLDER_ID")
									->where("folders.COMPANY_ID","=", $currentCompany)
									->where("USER_ID", $user->id)
									->orderBy("documents.created_at","DESC")->limit(10);			
		}
		$data = $lastUploads->get()->each(function($item, $key){
			$item->DOCUMENT_NAME = '<a class="file-preview" data-id="' .$item->DOCUMENT_ID .'" data-type="' 
									.$item->FILE_MIME .'" data-filename="' .$item->FILE_NAME
									.'" href="#">' .$item->DOCUMENT_NAME .'</a>';
			return $item;
		});
		return view('layouts.dashboard', ["last_uploads" => $data]);
	}

}

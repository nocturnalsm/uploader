<?php 

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\User;
use App\Companies;
use App\Documents;
use App\UserSettings;
use App\Notifications;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Validator;
use DB;
use Auth;

class UserController extends Controller {

	public function __construct()
	{				
		$this->middleware('permission:user.list', ["except" => "resetPassword"]);		
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		$columns = ["name","email","roles","last_upload"];
		if ($request->ajax()){            
			$data = User::select("id", "username", "name", "email", "roles.roles", "maxupload.last_upload", "aktif") 
						  ->leftJoin(
							DB::raw("(select model_has_roles.model_id, 
									   group_concat(roles.name) as roles
									   from model_has_roles 
									   inner join roles on roles.id = model_has_roles.role_id
								 	   group by model_has_roles.model_id) roles"), 
							function($join){
								$join->on("users.id","=","roles.model_id");
							}) 
						  ->leftJoin(
							DB::raw("(select USER_ID, 
									 MAX(documents.created_at) as last_upload
									 from documents								     
									 group by USER_ID) maxupload"),
							function($join){
								$join->on("users.id","=","maxupload.USER_ID");
							});                        
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
			return ["draw" => intval($request->draw), "recordsTotal" => $totalData,
					"recordsFiltered" => $totalFiltered,
					"data" => $data->get() 
				   ];
		}
		else {
			return view ("users.index");
		}
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		if (!Auth::user()->can('user.create')){
			$response = view("partials.flash", ["type" => "error", "text" => "Anda tidak memiliki hak akses"]);
			return response($response,500);
		}
		$data = new User;		
        $companies = Companies::get();
        $roles = Role::select("name")->get();
		return view('users.form', ["data" => $data, "companies" => $companies, "userCompanies" => [],
								   "roles" => $roles, "action" => "add", "userRoles" => []]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		if (!Auth::user()->can('user.create')){
			$response = view("partials.flash", ["type" => "error", "text" => "Anda tidak memiliki hak akses"]);
			return response($response,500);
		}
		$rules = [
					'name' => 'required',
					'username' => 'required|unique:users',
					'email' => 'required|email|unique:users',
					'password' => "required|same:confirm",
					'roles' => 'required'
				 ];
		if (!Auth::user()->hasRole('Super Admin')){
			$rules["companies"] = 'required';
		}
		$v = Validator::make($request->all(), $rules);		
		if ($v->fails())
		{
			return response()->json(["errors" => $v->errors()]);
		}
		DB::beginTransaction();
		try {
			$user = new User;                        
			$user->password = Hash::make($request->password);                        
			$user->username = $request->username;
			$user->email = $request->email;
			$user->name = $request->name;        
			$user->save();	
			foreach($request->roles as $role){	
				$user->assignRole($role);
			}			
			if (is_array($request->companies)){
				foreach ($request->companies as $comp){
					DB::table("user_companies")->insert(["USER_ID" => $user->id, 
														 "COMPANY_ID" => $comp]);
				}
			}			
			// create user default settings
			$settings = new UserSettings;
			$settings->USER_ID = $user->id;			
			$settings->SETTINGS = json_encode(
									["current_company" => $request->companies[0],
									 "notification_menu_max" => 10										
									]);
			$settings->save();
			// send welcome notification
			Notifications::insert([
				"USER_ID" => $user->id,
				"NOTIFICATION" => "Selamat datang, " .substr($user->name, 0, strpos($user->name," ")),
				"EXPIRED_TIME" => Date("Y-m-d H:i:s", strtotime(Date("Y-m-d") ." +7 days"))
			]);
			DB::commit();
			return view("partials.flash", ["type" => "success", "text" => "Penyimpanan Berhasil"]);
		}
		catch (Exception $e){
			DB::rollBack();
			return view("partials.flash", ["type" => "warning", "text" => "Penyimpanan data gagal.<br>" .$e->getMessage()]);
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
		if (!Auth::user()->can('user.edit')){
			$response = view("partials.flash", ["type" => "error", "text" => "Anda tidak memiliki hak akses"]);
			return response($response,500);
		}
		$data = User::where("id", $id)->first();
		$userCompanies = DB::table("user_companies")
							->select("COMPANY_ID")
							->where("USER_ID", $id)->get();
		$arrCompany = [];
		foreach($userCompanies as $uc){
			$arrCompany[] = $uc->COMPANY_ID;
		}
		$companies = Companies::get();
		$roles = Role::select("name")->get();
		$userRoles = DB::table("model_has_roles")
							->select("name")
							->join("roles","model_has_roles.role_id","=","roles.id")
							->where("model_id", $id)
							->get();
		$arrRoles = [];
		foreach($userRoles as $ur){
			$arrRoles[] = $ur->name;
		}
		return view('users.form', ["data" => $data, "companies" => $companies,
								   "userCompanies" => $arrCompany, "userRoles" => $arrRoles,
								   "roles" => $roles, "action" => "edit"]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id, Request $request)
	{
		if (!Auth::user()->can('user.edit')){
			$response = view("partials.flash", ["type" => "error", "text" => "Anda tidak memiliki hak akses"]);
			return response($response,500);
		}
		$v = Validator::make($request->all(), [
			'username' => 'required|unique:users,username,' .$id,
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' .$id,
            'password' => 'same:confirm',
            'roles' => 'required'
		]);
		if ($v->fails())
		{
			return response()->json(["errors" => $v->errors()]);
		}
		DB::beginTransaction();
		try {
			$user = User::find($id);            
			if (!empty($request->password)){
				$user->password = Hash::make($request->password);
			}
			$user->username = $request->username;
			$user->email = $request->email;
			$user->name = $request->name;        
			$user->aktif = $request->aktif == "Y" ? $request->aktif : "T";
			$user->save();
			
			DB::table('model_has_roles')->where('model_id',$user->id)->delete();    
			foreach($request->roles as $role){
				$user->assignRole($role);
			}	
			$userCompanies = DB::table("user_companies")
								->select("COMPANY_ID")
								->where("USER_ID", $id)->get();
			$arrCompany = [];
			foreach($userCompanies as $uc){
				$arrCompany[] = $uc->COMPANY_ID;
			}
			$reqCompanies = is_array($request->companies) ? $request->companies : [];
			
			$diff1 = array_diff($reqCompanies, $arrCompany);
			foreach($diff1 as $df1){
				DB::table("user_companies")->insert(["USER_ID" => $id, "COMPANY_ID" => $df1]);
			}
			$diff2 = array_diff($arrCompany, $reqCompanies);
			$warnings = [];
			foreach($diff2 as $df2){
				$check = Documents::join("user_companies","documents.USER_ID","=","user_companies.USER_ID")
						->where("documents.USER_ID", $id)
						->where("user_companies.COMPANY_ID", $df2);
				if ($check->count() > 0){
					$name = DB::table("companies")->where("COMPANY_ID", $df2)->pluck("NAME");
					$warnings[] = "User ini memiliki dokumen di perusahaan " .$name;
				}
				else {
					DB::table("user_companies")->where("USER_ID", $id)
											->where("COMPANY_ID", $df2)
											->delete();
				}
			}
			DB::commit();
			if (count($warnings) > 0){
				return view("partials.flash", ["type" => "warning", "text" => implode("<br>", $warnings)]);
			}
			else {
				return view("partials.flash", ["type" => "success", "text" => "Update berhasil"]);
			}
		}
		catch (Exception $e){
			DB::rollBack();
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
		if (!Auth::user()->can('user.delete')){
			$response = view("partials.flash", ["type" => "error", "text" => "Anda tidak memiliki hak akses"]);
			return response($response,500);
		}
		try {
			$data = User::find($request->id)->delete();			
			return view("partials.flash", ["type" => "info", "text" => "Data berhasil dihapus"]);
		}
		catch (Exception $e){
			$response = view("partials.flash", ["type" => "error", "text" => "Penghapusan data gagal"]);
			return response($response, 500)
							->header('Content-Type', 'text/html');
		}
	}	
}

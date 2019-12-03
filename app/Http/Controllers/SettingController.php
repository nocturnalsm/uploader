<?php 

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Companies;
use App\UserSettings;
use App\Settings;
use Validator, Auth, Hash;

class SettingController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		$companies = Auth::user()->companies()->get();
		$settings = Auth::user()->settings();				
		$appSettings = Settings::pluck("SETTING_VALUE","SETTING_NAME");
		return view ("layouts.settings", ["companies" => $companies,
										  "app_settings" => $appSettings->all(),
                                          "user_settings" => $settings]);		
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$rules = ['notification_menu_max' => 'required|max:10'];
		$user = Auth::user();
		if (!$user->hasRole("Super Admin")){
			$rules["current_company"] = 'required';
		}
		$v = Validator::make($request->all(), $rules,
						["current_company.required" => "Nama perusahaan harus dipilih",
						"notification_menu_max" => "Jumlah Notifikasi yang ditampilkan harus diisi",
						"google_drive_username" => "Username Google Drive harus diisi",
						"google_drive_upload_folder" => "ID Folder untuk upload Drive harus diisi"
						]);
	
		if ($v->fails())
		{			
			return redirect()->back()->withInput()->withErrors($v->errors());
		}		
		$settings["current_company"] = $request->current_company;
		$settings["notification_menu_max"] = $request->notification_menu_max;		
		try {			
			$data = UserSettings::where("USER_ID", $user->id);
			if ($data->count() == 0){
				$data = new UserSettings;
				$data->USER_ID = $user->id;
			}
			else {
				$data = $data->first();
			}
			$data->SETTINGS = json_encode($settings);
			$data->save();
			if ($user->hasRole('Super Admin')){
				$app_settings["google_drive_username"] = $request->google_drive_username;
				$app_settings["google_drive_upload_folder"] = $request->google_drive_upload_folder;			   
				Settings::set($app_settings);				
			}			
			return redirect("settings")->with(["type" => "success", "text" => "Penyimpanan Berhasil"]);
		}
		catch (Exception $e){
			return redirect("settings")->with(["type" => "warning", "text" => "Penyimpanan data gagal.<br>" .$e->getMessage()]);
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
		
	}	

	public function resetPassword(Request $request)
	{
		$v = Validator::make($request->all(), [
            'password' => 'required|same:confirm',
		]);
		if ($v->fails())
		{
			return redirect("settings")->withErrors($v->errors());
		}
		try {
			$user = Auth::user();
			$user->password = Hash::make($request->password);
			$user->save();
			return redirect("settings")->with(["type" => "info", "text" => "Password berhasil diubah"]);
		}
		catch (Exception $e){
			return redirect("settings")->with(["type" => "error", "text" => "Perubahan password gagal"]);
		}
	}
	public function setCurrentCompany(Request $request)
	{
		$user = Auth::user();
		$settings = $user->settings();
		$settings["current_company"] = $request->_current_company;
		$userSetting = UserSettings::where("USER_ID", $user->id)->first();
		$userSetting->SETTINGS = json_encode($settings);
		$userSetting->save();
		return redirect()->back();				 
	}

}

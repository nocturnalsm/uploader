<?php 

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

	public function companies()
	{		
		if (auth()->user()->hasRole("Super Admin")){
			$companies = Companies::select("NAME","COMPANY_ID")									
									->orderBy("companies.NAME");
		}
		else {
			$companies = $this->select("companies.NAME","companies.COMPANY_ID")
						 ->join("user_companies", "users.id","=","user_companies.USER_ID")
						 ->join("companies", "companies.COMPANY_ID","=","user_companies.COMPANY_ID")						 						 
						 ->where("user_companies.USER_ID", $this->id)
						 ->orderBy("companies.NAME");		
		}
		return $companies;
	}	
	public function settings(...$key)
	{				
		$settings = UserSettings::where("USER_ID", $this->id)->value("SETTINGS");						
		$decoded = json_decode($settings, true);		
		if (count($key) == 0){
			return $decoded;
		}
		else {
			foreach ($key as $item){
				if (is_string($item)){
					return isset($decoded[$item]) ? $decoded[$item] : "";
				}
				else if (is_array($item)){
					$return = Array();
					foreach($item as $itm){
						if (isset($decoded[$itm])){
							$return[$itm] = $decoded[$itm];
						}
					}
					return $return;
				}
			}			
		}
	}
}

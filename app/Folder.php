<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model {

	protected $primaryKey = 'FOLDER_ID'; // or null  

	public static function copy($source, $destination){
		$children = self::where("PARENT_ID", $source)->get()->each(function($data) use ($destination){			
			$destFolder = self::where("FOLDER_ID", $destination)->first();
			$newFolder = new Folder();
			$newFolder->COMPANY_ID = $destFolder->COMPANY_ID;
			$newFolder->FOLDER_NAME = $data->FOLDER_NAME;
			$newFolder->PARENT_ID = $destination;
			$newFolder->save();			
			self::copy($data->FOLDER_ID, $newFolder->FOLDER_ID);
		});
	}
}

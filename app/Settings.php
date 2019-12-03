<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model {
    protected $primaryKey = 'SETTING_ID'; // or null
 
    public static function set($settings)
    {
        foreach ($settings as $key=>$set){
            $data = self::where("SETTING_NAME", $key)->first();
            $data->SETTING_VALUE = $set;
            $data->save();
        }
    }
    public static function get($key)
    {
        if (is_array($key)){
            $settings = self::whereIn('SETTING_NAME', $key);
            if ($settings->count() > 0){
                return $settings->pluck("SETTING_VALUE", "SETTING_NAME");
            }
            else {
                return [];
            }
            
        }
        else {
            $setting = self::where("SETTING_NAME",$key);
            if ($setting->count() > 0){
                return $setting->value("SETTING_VALUE");
            }
            else {
                return "";
            }
        }
    }
}

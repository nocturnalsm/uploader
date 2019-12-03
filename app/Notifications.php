<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Notifications extends Model {
	
	protected $primaryKey = 'NOTIFICATION_ID'; // or null    
    protected $count;
    protected $maxNotification;
    protected $fillable = ["USER_ID","NOTIFICATION","EXPIRED_TIME","PRIORITY","URL"];

    public function __construct()
    {
        $this->maxNotification = 10;
    }
    public function get($max = 0)
    {
        if ($max == 0){
            $max = $this->maxNotification;
        }
        $data = $this->where("USER_ID", Auth::user()->id)
                     ->whereRaw("(EXPIRED_TIME IS NULL OR EXPIRED_TIME > '" .Date("Y-m-d H:i:s") ."')")
                     ->orderBy("created_at", "DESC")                     
                     ->limit($max);
        $this->count = $data->count();
        return $data->get();
    }
    public function count()
    {
        if (!isset($this->count)){
            $this->get();
        }
        return $this->count;
    }
}

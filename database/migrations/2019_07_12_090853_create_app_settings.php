<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('SETTING_ID');
            $table->string('SETTING_NAME');
            $table->string('SETTING_VALUE');
            $table->timestamps();            
        });

        Schema::table('settings', function(Blueprint $table)
		{
            DB::table("settings")->insert([
                ["SETTING_NAME" => 'google_drive_username',
                 "SETTING_VALUE" => ""],
                ["SETTING_NAME" => 'google_drive_upload_folder',
                 "SETTING_VALUE" => ""]
            ]);
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

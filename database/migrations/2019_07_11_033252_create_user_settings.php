<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("user_settings", function (Blueprint $table) {
            $table->increments('USERSETTINGS_ID');            
            $table->integer("USER_ID");
            $table->string('SETTINGS');
            $table->timestamps();
        });
        DB::table("user_settings")->insert(
            [
                "USER_ID" => 1,
                "SETTINGS" => json_encode(["last_company" => "", 
                                           "notification_menu_max" => 10])
            ]
        );
        Schema::create("notifications", function (Blueprint $table) {
            $table->increments('NOTIFICATION_ID');            
            $table->integer("USER_ID");
            $table->string('NOTIFICATION');
            $table->datetime("EXPIRED_TIME")->nullable();
            $table->integer("PRIORITY")->default(0);
            $table->string('URL')->nullable();
            $table->timestamps();
        });
        DB::table("notifications")->insert(
            [
                "USER_ID" => 1,
                "NOTIFICATION" => "Selamat Datang, {{ username }}",
                "EXPIRED_TIME" => Date("Y-m-d"),
                "URL" => ""
            ]
        );
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

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompanyField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("companies", function($table){
            $table->char("COMPANY_TYPE",1)->default("B")->index();
            $table->string("NPWP",20)->default("");
            $table->string("ALAMAT")->default("");
            $table->string("KOTA", 100)->default("");
            $table->string("TELEPON", 100)->default("");
            $table->string("DIREKTUR_UTAMA")->default("");
            $table->string("NPWP_DIREKTUR_UTAMA", 20)->default("");
            $table->string("EMAIL_PKP")->default("");
            $table->string("PASSPHRASE")->default("");
            $table->string("USERNAME_EFAKTUR")->default("");
            $table->string("PASSWORD_EFAKTUR")->default("");
            $table->string("PASSWORD_UPLOAD")->default("");
            $table->string("EFIN")->default("");
            $table->string("EMAIL_DJP")->default("");
            $table->string("PASSWORD_DJP")->default("");
        });
        Schema::create('direktur', function(Blueprint $table)
		{
            $table->increments('DIREKTUR_ID');
            $table->integer("COMPANY_ID")->index();
			$table->string('NAMA')->default("");
			$table->string('NPWP', 20)->default("");
        });
        Schema::create('komisaris', function(Blueprint $table)
		{
            $table->increments('KOMISARIS_ID');
            $table->integer("COMPANY_ID")->index();
			$table->string('NAMA')->default("");
			$table->string('NPWP', 20)->default("");
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

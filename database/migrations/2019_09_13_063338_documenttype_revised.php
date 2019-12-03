<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DocumenttypeRevised extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Schema::rename("document_types", "folders");
        
        Schema::table("folders", function($table){
            $table->integer("PARENT_ID")->default(0)->after("DOCUMENTTYPE_ID");
            $table->renameColumn("DOCUMENTTYPE_ID", "FOLDER_ID");
            $table->renameColumn("DOCUMENT_TYPE", "FOLDER_NAME");            
        });
        Schema::table("documents", function($table){
            $table->renameColumn("DOCUMENT_TYPE", "FOLDER_ID");
            $table->dropColumn("USERCOMPANY_ID");
        });
        Schema::table("companies", function($table){
            $table->integer("FOLDER_ID")->after("COMPANY_ID");
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename("folders", "document_types");
        Schema::table("document_types", function($table){
            $table->renameColumn("FOLDER_ID", "DOCUMENTTYPE_ID");
            $table->renameColumn("FOLDER_NAME", "DOCUMENT_TYPE");
            $table->dropColumn("PARENT_ID");
        });   
        Schema::table("documents", function($table){
            $table->renameColumn("FOLDER_ID", "DOCUMENT_TYPE");
        });     
    }
}

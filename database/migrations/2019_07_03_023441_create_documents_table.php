<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('documents', function(Blueprint $table)
		{
			$table->increments('DOCUMENT_ID');
			$table->string('FILE_ID');
			$table->string('FILE_NAME');
			$table->string('FILE_MIME');
			$table->string('DOCUMENT_NAME');
			$table->integer('DOCUMENT_TYPE');
			$table->integer('USERCOMPANY_ID');
			$table->timestamps();
		});
		Schema::create('companies', function(Blueprint $table)
		{
			$table->increments('COMPANY_ID');
			$table->string('NAME');			
			$table->timestamps();
		});
		Schema::create('document_types', function(Blueprint $table)
		{
			$table->increments('DOCUMENTTYPE_ID');
			$table->string('DOCUMENT_TYPE');			
			$table->timestamps();
		});
		Schema::create('user_companies', function(Blueprint $table)
		{
			$table->increments('USERCOMPANY_ID');
			$table->integer('USER_ID');
			$table->integer('COMPANY_ID');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('documents', function(Blueprint $table)
		{
			Schema::drop('companies');
			Schema::drop('documents');
			Schema::drop('document_types');
			Schema::drop('user_companies');
			Schema::drop('notification');
		});
	}

}

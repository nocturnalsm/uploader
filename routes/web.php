<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'HomeController@index');

//Route::get('auth/logout', 'Auth\AuthController@getLogout')->name("logout");
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

Route::group(['middleware' => ['auth']], function(){	
	Route::resource('/notifications', 'NotificationController');
	Route::post("settings/resetpassword", 'SettingController@resetPassword')->name("settings.resetpassword");
	Route::post("settings/current_company", 'SettingController@setCurrentCompany')->name("settings.current_company");
	Route::resource('/settings', 'SettingController');
});

Route::group(['middleware' => ['auth']], function(){
	Route::get("document/get", 'DocumentController@getFile')->name("document.get");
	Route::post("document/upload", 'DocumentController@upload')->name("document.upload");
	Route::resource('document','DocumentController');
});

Route::group(['prefix' => 'master', 'middleware' => ['auth']], function () {  
	Route::post("company/showpass", 'CompanyController@getPassword')->name("company.showpass");;
	Route::resource('folder','FolderController');
	Route::resource('company','CompanyController');
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function () {  	
	Route::resource('users','UserController');
	Route::resource('roles','RoleController');
});

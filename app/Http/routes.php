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
if (isset($_SERVER['HTTP_ORIGIN'])) {
	header('Access-Control-Allow-Credentials: true');
	header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
	header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH');
	header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, useXDomain, withCredentials');
	header('Keep-Alive: off');
}

Route::group(['middleware' => 'cors'], function()
{
	Route::post('session', 'AuthenticateController@authenticate');

	//Menu
	Route::get('menu/getMenu','MenuController@getMenu2');
	Route::post('menu/cu','MenuController@CU');
	Route::patch('menu/sortMenu','MenuController@sortMenu');
	Route::delete('menu/del/{id}','MenuController@destroy');

	//Role
	Route::get('role/listRole','RoleController@getRole');
	Route::get('role/getRole/{id}','RoleController@show');
	Route::post('role/addEditRole','RoleController@addEditRole');
	Route::delete('role/deleteRole/{id}','RoleController@destroy');

	//Role Menu
	Route::get('role/listRoleMenu','RoleController@getRoleMenu');
	Route::patch('role/updateRoleMenu','RoleController@updateRoleMenu');

	Route::get('404', ['as' => 'notfound', function () {
		return response()->json(['status' => '404']);
	}]);

	Route::get('405', ['as' => 'notallow', function () {
		return response()->json(['status' => '405']);
	}]);	
});
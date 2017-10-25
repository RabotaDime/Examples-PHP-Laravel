<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get		('users'			, ['uses' => 'UsersController@index']);
Route::get		('users/create'		, ['uses' => 'UsersController@create']);
Route::post		('users'			, ['uses' => 'UsersController@store']);

Route::get		('vehicles/routes', [
					'uses'	=> 'VehicleWaypointsController@index',
					'as'	=> 'vehicles.routes.index'
				]);

Route::post		('vehicles/routes', [
					'uses'	=> 'VehicleWaypointsController@import',
					'as'	=> 'vehicles.routes.import'
				]);

Auth::routes();

Route::prefix('vehicles')->middleware('auth')->group(function () {

	///   Web: Просмотр выбранного пути. 
	Route::get	  ('{vehicle_id}/routes/{route_id}', 'VehicleWaypointsController@show')->
				  where([
				      'vehicle_id'	=> '[0-9]+',
					  'route_id'	=> '[0-9]+'
				  ]);

});

Route::prefix('api/vehicles')->middleware('auth')->group(function () {

	///   REST API: Вывод данных по выбранному пути. 
	Route::get	  ('{vehicle_id}/routes/{route_id}', 'VehicleWaypointsController@api_show')->
				  where([
				      'vehicle_id'	=> '[0-9]+',
					  'route_id'	=> '[0-9]+'
				  ]);

});


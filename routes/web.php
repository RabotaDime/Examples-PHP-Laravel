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

Route::domain('api.waypoints.ru')->prefix('vehicles')->middleware('auth')->group(function () {

	///   REST API: Вывод данных по выбранному пути. 
	Route::get    ('{vehicle_id}/routes/{route_id}', 'VehicleWaypointsController@api_show')->
				  where([
				      'vehicle_id'	=> '[0-9]+',
					  'route_id'	=> '[0-9]+'
				  ]);

	Route::post   ('{vehicle_id}/routes/create', [
						'uses'	=> 'VehicleWaypointsController@import',
						'as'	=> 'vehicles.routes.import'
					]);

});

Route::domain('waypoints.ru')->prefix('vehicles')->middleware('auth')->group(function () {

	Route::get('/', function () {
		return view('welcome');
	});

	///   Web: Просмотр выбранного пути. 
	Route::get	  ('{vehicle_id}/routes/{route_id}', 'VehicleWaypointsController@show')->
				  where([
				      'vehicle_id'	=> '[0-9]+',
					  'route_id'	=> '[0-9]+'
				  ]);

	Route::get	  ('users'			, ['uses' => 'UsersController@index']);
	Route::get	  ('users/create'	, ['uses' => 'UsersController@create']);
	Route::post	  ('users'			, ['uses' => 'UsersController@store']);

	Route::get	  ('home', 'HomeController@index')->name('home');

});


Auth::routes();


/*
Route::get		('vehicles/routes', [
					'uses'	=> 'VehicleWaypointsController@index',
					'as'	=> 'vehicles.routes.index'
				]);
*/


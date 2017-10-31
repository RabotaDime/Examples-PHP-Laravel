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

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;



$SiteBaseAddress = 'waypoints.test';



Route::domain('api.' . $SiteBaseAddress)->prefix('vehicles')->middleware('auth')->group(function ()
{

	///   REST API: Вывод данных по выбранному пути. 
	Route::get    ('{vehicle_id}/routes/{route_id}', 'VehicleWaypointsController@api_show')->
				  where([
				      'vehicle_id'	=> '[0-9]+',
					  'route_id'	=> '[0-9]+',
				  ]);

	Route::post   ('{vehicle_id}/routes/create', [
						'uses'	=> 'VehicleWaypointsController@api_import_direct',
						'as'	=> 'api.vehicles.routes.import'
					]);

});

Route::domain($SiteBaseAddress)->prefix('vehicles')->middleware('auth')->group(function ()
{

	///   Web: Просмотр выбранного пути. 
	Route::get	  ('{vehicle_id}/routes/{route_id}/', 'VehicleWaypointsController@show')->
				  where([
				      'vehicle_id'	=> '[0-9]+',
					  'route_id'	=> '[0-9]+',
				  ])->
				  name('vehicle_route');

	Route::get	  ('{vehicle_id}/routes/', 'VehicleWaypointsController@show_list')->
				  where([
				      'vehicle_id'	=> '[0-9]+',
				  ])->
				  name('vehicle_routes');

	Route::get	  ('{vehicle_id}/routes/create/', 'VehicleWaypointsController@import_direct')->
				  where([
				      'vehicle_id'	=> '[0-9]+',
				  ])->
				  name('vehicle_route_import');

	Route::get	  ('{vehicle_id}/routes/create-debug/', 'VehicleWaypointsController@import_debug')->
				  where([
				      'vehicle_id'	=> '[0-9]+',
				  ])->
				  name('vehicle_route_import_debug');

	Route::get	  ('users'			, ['uses' => 'UsersController@index']);
	Route::get	  ('users/create'	, ['uses' => 'UsersController@create']);
	Route::post	  ('users'			, ['uses' => 'UsersController@store']);


/*
	Route::get('/', function () {
		return view('welcome');
	});

	Route::get	  ('home', 'HomeController@index')->name('home');
*/
});


Route::domain($SiteBaseAddress)->middleware('auth')->group(function ()
{

	Route::get		('/', 'VehicleWaypointsController@home')->name('home');

});


Auth::routes();


/*
Route::get		('vehicles/routes', [
					'uses'	=> 'VehicleWaypointsController@index',
					'as'	=> 'vehicles.routes.index'
				]);
*/


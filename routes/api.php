<?php

use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('/time', function(){
    return Carbon::now();
});
Route::get('/{sid}/booking', 'BookingController@index');
Route::post('/{sid}/booking/create', 'BookingController@create');
// Route::get('/booking/{sid}/{id}', 'BookingController@show');
Route::get('/{sid}/shop-plan', 'ShopPlanController@index');
// Route::get('/shop-plan/{sid}/{id}', 'ShopPlanController@show');
Route::get('/{sid}/user', 'UserController@index');

Route::resource('/{sid}/user', 'UserController', ['only' => ['show', 'update', 'store']]);
Route::resource('/{sid}/booking', 'BookingController', ['only' => ['show', 'update', 'store']]);
// Route::resource('booking', 'BookingController', ['except' => ['index', 'create', 'edit', 'show']]);
Route::resource('/{sid}/payment', 'PaymentController', ['except' => ['index', 'create', 'edit']]);
Route::resource('/{sid}/shop-plan', 'ShopPlanController', ['only' => ['show', 'update', 'store']]);

// Route::get('/booking/create', 'BookingController@create');
// Route::post('/booking', 'BookingController@store');
// Route::post('/booking/{id}/edit', 'BookingController@edit');
// Route::put('/booking/{id}', 'BookingController@update');
// Route::delete('/booking/{id}', 'BookingController@destroy');
// Route::get('/booking/{sid}/{id}', 'CalendarController@show');

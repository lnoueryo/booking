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
Route::get('/booking/{sid}', 'BookingController@index');
Route::get('/shop-plan/{sid}', 'ShopPlanController@index');
Route::get('/booking/{sid}/{id}', 'BookingController@show');

Route::resource('booking', 'BookingController', ['except' => ['index', 'create', 'edit', 'show']]);
Route::resource('payment', 'PaymentController', ['except' => ['index', 'create', 'edit']]);
Route::resource('shop-plan', 'ShopPlanController', ['except' => ['index', 'create', 'edit']]);

// Route::get('/booking/create', 'BookingController@create');
// Route::post('/booking', 'BookingController@store');
// Route::post('/booking/{id}/edit', 'BookingController@edit');
// Route::put('/booking/{id}', 'BookingController@update');
// Route::delete('/booking/{id}', 'BookingController@destroy');
// Route::get('/booking/{sid}/{id}', 'CalendarController@show');

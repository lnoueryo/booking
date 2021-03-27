<?php
use App\Staff;
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
Route::get('/api/check', function(){
    if(Auth::check()){
        $user = Staff::with(['shop'])->find(Auth::id());
        return $user;
    } else {
        return;
    }
});
Auth::routes();


Route::get('/{any?}', function () {
    return view('welcome');
})->where('any','^(?!api\/)[\/\w\.-]*');


Route::get('/home', 'HomeController@index')->name('home');
<?php

use Illuminate\Http\Request;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('signup' , 'Auth\LoginController@register');
Route::post('login' , 'Auth\LoginController@login');

Route::group(['middleware' => ['jwt']] , function () {
    //Route::get('getCurrentUser' , 'Auth\LoginController@user');
    Route::post('wagers' , 'WagerController@store');
    Route::post('buy/{wagerId}' , 'WagerController@buyAWager');
    Route::get('wagers' , 'WagerController@index');
});
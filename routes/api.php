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

Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');

Route::middleware('auth:api')->group(function () {
    Route::get('/me', 'API\UserController@details');
    Route::get('/places','API\PlaceController@search');
    Route::get('/recommend', 'API\RecommendController@index');    Route::post('/review', 'API\ReviewController@store');    
});

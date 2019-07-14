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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/register', 'AuthController@register');
Route::post('/login', 'AuthController@login');
Route::post('/logout', 'AuthController@logout');



Route::get('open', 'TestController@open');

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('user', 'UserController@getAuthenticatedUser');
    Route::get('closed', 'DataController@closed');
});


//list articles
Route::get('articles','ArticleController@index');

//list single article
Route::get('article/{id}','ArticleController@show');

//create new article
Route::post('article','ArticleController@store');

//update article
Route::put('article','ArticleController@store');

//update article
Route::delete('article/{id}','ArticleController@destroy');



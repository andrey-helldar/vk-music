<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', [
    'as'   => 'index',
    'uses' => 'IndexController@getIndex',
]);

Route::get('verify', [
    'middleware' => ['guest'],
    'uses'       => 'IndexController@getVerify',
]);

Route::get('storage/{slug?}', 'IndexController@getStorageBlocked');

/**
 * Скачивание файла.
 */
Route::get('download/{id?}', [
    'as'         => 'download',
    'middleware' => ['auth'],
    'uses'       => 'IndexController@getDownloadFile',
]);

Route::get('logout', [
    'as'         => 'logout',
    'middleware' => ['auth'],
    'uses'       => 'IndexController@getLogout',
]);

//Auth::routes();

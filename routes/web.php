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

Route::get('verify', 'IndexController@getVerify');

/**
 * Скачивание файла пользователем.
 */
Route::get('download/{id?}', [
    'as'   => 'download',
    'uses' => 'IndexController@getDownloadFile',
]);

Route::get('{slug?}', [
    'as'   => 'index',
    'uses' => 'IndexController@getIndex',
]);

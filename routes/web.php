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

/**
 * Получение списка контактов.
 */
Route::get('friends', [
    'as'   => 'friends',
    'uses' => 'IndexController@getFriends',
]);

/**
 * Получение списка групп пользователя.
 */
Route::get('groups', [
    'as'   => 'groups',
    'uses' => 'IndexController@getGroups',
]);

/**
 * Скачивание файла.
 */
Route::get('download/{id?}', [
    'as'         => 'download',
    'middleware' => ['auth'],
    'uses'       => 'IndexController@getDownloadFile',
]);

Route::get('logout', [
    'as'   => 'logout',
    'uses' => 'IndexController@getLogout',
]);

//Auth::routes();

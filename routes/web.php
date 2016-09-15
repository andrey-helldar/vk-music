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
 * Получение аудиозаписей конкретного пользователя.
 */
Route::get('friends/{slug}', 'IndexController@getFriendsSlug');

/**
 * Получение списка групп пользователя.
 */
Route::get('groups', [
    'as'   => 'groups',
    'uses' => 'IndexController@getGroups',
]);
/**
 * Получение списка аудио конкретной группы.
 */
Route::get('groups/{slug}', 'IndexController@getGroupsSlug');

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

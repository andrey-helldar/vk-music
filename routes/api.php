<?php

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

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:api');

Route::get('top.menu', 'Api\AppController@getTopMenu');
Route::get('main.blocks', 'Api\AppController@getMainBlocks');
Route::get('vk.params', 'Api\AppController@getParams');
Route::post('vk.verify', 'Auth\VkController@postVerify');

Route::group([
    'middleware' => [
        'auth.check',
    ],
], function () {
    // Получение идентификаторов жанров.
    Route::get('audio.genres', 'Api\AudioController@getGenres');
    // Запрос на формирование ссылки для скачивания файла.
    Route::post('download', 'Api\AppController@postDownloadFile');


    // Запрос треков пользователя.
    Route::post('audio.user', 'Api\AudioController@storeAudio');
    // Получение треков пользователя.
    Route::get('audio.user', 'Api\AudioController@getAudio');

    // Запрос рекомендуемых треков пользователя.
    Route::post('audio.recommendations', 'Api\AudioController@storeRecommendations');
    // Получение рекомендуемых треков пользователя.
    Route::get('audio.recommendations', 'Api\AudioController@getRecommendations');

    // Запрос популярных треков.
    Route::post('audio.popular', 'Api\AudioController@storePopular');
    // Получение треков пользователя.
    Route::get('audio.popular', 'Api\AudioController@getPopular');

    /**
     * Запрос на поиск треков.
     */
    Route::post('audio.search', 'Api\AudioController@storeSearch');
    /**
     * Получение найденных треков.
     */
    Route::get('audio.search', 'Api\AudioController@getSearch');

    /**
     * Копирует аудиозапись на страницу пользователя или группы.
     */
    Route::post('audio.add', 'Api\AudioController@storeAdd');

    // Запрос списка контактов.
    Route::post('friends.user', 'Api\FriendsController@storeFriends');
    //Получение списка контактов.
    Route::get('friends.user', 'Api\FriendsController@getFriends');

    // Запрос списка групп.
    Route::post('groups.user', 'Api\GroupsController@storeGroups');
    //Получение списка групп.
    Route::get('groups.user', 'Api\GroupsController@getGroups');

    // Получение информации о текущем пользователе, записанной в локальной базе.
    Route::get('current.user.info', 'Api\AppController@getCurrentUserInfo');
});

//Route::any('{slug?}', function () {
//    return \VKMUSIC\Http\Controllers\Api\ResponseController::error(1);
//});
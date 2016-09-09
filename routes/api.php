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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::get('topmenu', 'Api\AppController@getTopmenu');
Route::get('vk.params', 'Api\AppController@getParams');
Route::post('vk.verify', 'Auth\VkController@postVerify');

Route::group([
    'middleware' => [
        'auth.check',
        'auth:api',
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


});

Route::any('{slug?}', function () {
    return \VKMUSIC\Http\Controllers\Api\ResponseController::error(1);
});
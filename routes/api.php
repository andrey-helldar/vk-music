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

Route::group([
    'middleware' => [
        'auth:api',
        'auth.check',
    ],
], function () {
    // Добавление запроса списка.
    Route::post('audios.user', 'Api\AudiosController@storeAudios');
    Route::post('audios.popular', 'Api\AudiosController@getPopularAudios');

    // Проверка ответов.
    Route::get('audios.user', 'Api\AudiosController@getAudios');
    // Получение идентификаторов жанров.
    Route::get('audios.genres', 'Api\AudiosController@getGenres');
});

Route::any('{slug?}', function () {
    return \VKMUSIC\Http\Controllers\Api\ResponseController::error(1);
});
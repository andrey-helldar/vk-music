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

Route::get('vk.params', 'Auth\VkController@getParams');

Route::group([
    'middleware' => ['auth.check'],
], function () {
    Route::get('audios.user', 'Api\AudiosController@getUserAudios');
    Route::get('audios.popular', 'Api\AudiosController@getPopularAudios');
});

Route::any('{slug?}', function () {
    return \VKMUSIC\Http\Controllers\Api\ResponseController::error(1);
});
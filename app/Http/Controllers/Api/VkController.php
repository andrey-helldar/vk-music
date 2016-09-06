<?php

namespace VKMUSIC\Http\Controllers\Api;

use Carbon\Carbon;
use VKMUSIC\Http\Controllers\Controller;
use VKMUSIC\Http\Requests;
use VKMUSIC\VkQueue;
use VKMUSIC\VkResponse;

class VkController extends Controller
{

    /**
     * Добавление задания в очередь.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-02
     * @since   1.0
     *
     * @param $method
     * @param $context
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public static function createRequest($method, $context)
    {
        $user = \Auth::user();

        $validator = \Validator::make([
            'method'  => $method,
            'context' => $context,
        ], [
            'method'  => 'required|string|max:255',
            'context' => 'array',
        ]);

        if ($validator->fails()) {
            return ResponseController::error(0, $validator->errors()->all());
        }

        // Проверяем уникальность запроса.
        $queue = VkQueue::whereUserId($user->id)->whereMethod($method)->first();

        if (!is_null($queue)) {
            return ResponseController::error(20);
        }

        // Удаляем старые полученные данные.
        self::removeOldResponses($user->id, $method);

        if (Carbon::parse($user->token->expired_at) <= Carbon::now()) {
            return ResponseController::error(30);
        }

        $context = array_merge($context, [
            'v'            => config('vk.api_version'),
            'access_token' => $user->token->access_token,
        ]);

        $queue_created = VkQueue::create([
            'user_id'      => $user->id,
            'access_token' => $user->token->access_token,
            'method'       => $method,
            'context'      => json_encode($context),
        ]);

        $position = VkQueue::where('id', '<=', $queue_created->id)->count();

        return ResponseController::success(0, [
            'resolve'     => trans('api.10'),
            'description' => trans('api.12', ['position' => $position]),
        ]);
    }

    /**
     * Удаляем старые данные из таблицы, фильтруя по вызываемому методу.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-03
     * @since   1.0
     *
     * @param $user_id
     * @param $method
     */
    private static function removeOldResponses($user_id, $method)
    {
        VkResponse::whereUserId($user_id)->whereMethod($method)->delete();
    }
}

<?php

namespace VKMUSIC\Http\Controllers\Api;

use Illuminate\Http\Request;
use VKMUSIC\Http\Controllers\Controller;
use VKMUSIC\Http\Requests;
use VKMUSIC\VkQueue;
use VKMUSIC\VkResponse;

class AudiosController extends Controller
{
    /**
     * Возвращает список аудиозаписей пользователя или сообщества.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-02
     * @since   1.0
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    function storeAudio(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'offset'     => 'numeric|min:0',
            'owner_type' => 'string',
            'owner_id'   => 'string',
        ]);

        if ($validator->fails()) {
            return ResponseController::error(0, $validator->errors()->all());
        }

        return VkController::createRequest('audio.get', array_merge([
            'need_user' => 0,
            'offset'    => (int)($request->offset ?: 0),
            'count'     => config('vk.count_records', 20),
        ], $this->ownerId($request->owner_type, $request->owner_id)));
    }

    /**
     * Проверяем что нужно вернуть.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-02
     * @since   1.0
     *
     * @param $type
     * @param $id
     *
     * @return array
     */
    private function ownerId($type, $id)
    {
        switch ($type) {
            case 'group':
                $owner_id = abs($id) * -1;

            case 'user':
                $owner_id = abs($id);

            default:
                $owner_id = \Auth::user()->token->user_vk;
        }

        return [
            'owner_id' => $owner_id,
        ];
    }

    /**
     * Возвращает список аудиозаписей из раздела «Популярное».
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-02
     * @since   1.0
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    function getPopular(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'genre_id' => 'numeric|min:0|max:100',
            'offset'   => 'string',
        ]);

        if ($validator->fails()) {
            return ResponseController::error(0, $validator->errors()->all());
        }

        return VkController::createRequest('audio.getPopular', [
            'only_eng' => 0,
            'genre_id' => (int)($request->genre_id ?: 0),
            'offset'   => (int)($request->offset ?: 0),
            'count'    => config('vk.count_records', 20),
        ]);
    }

    function getGroup(Request $request)
    {
        return $this->getAudio();
    }

    /**
     * Возврат полученных от VK API данных.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-03
     * @since   1.0
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    function getAudio()
    {
        $user     = \Auth::user();
        $response = VkResponse::whereUserId($user->id)->whereMethod('audio.get')->where('updated_at', '<', $user->token->expired_at)->first();
        $position = $this->getQueuePosition('audio.get', $user->id);

        if (is_null($response)) {
            return ResponseController::error(0, [
                'resolve'     => trans('api.21'),
                'description' => trans('api.12', ['position' => $position]),
            ], 406);
        }

        $items = json_decode($response->context)->response;

        if (!empty($items->error)) {
            return ResponseController::error(0, [
                'resolve'     => trans('api.1'),
                'description' => trans('api.12', ['position' => $position]),
            ], 406);
        }

        $response->delete();

        return ResponseController::success(0, [
            'resolve'     => trans('api.40'),
            'items'       => $items->items,
            'count_all'   => $items->count,
            'count_query' => config('vk.count_records', 50),
        ]);
    }

    /**
     * Считаем позицию запроса пользователя в очереди.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-07
     * @since   1.0
     *
     * @param $method
     * @param $user_id
     *
     * @return int
     */
    private function getQueuePosition($method, $user_id)
    {
        $order = VkQueue::whereMethod($method)->whereUserId($user_id)->first();

        if (is_null($order)) {
            return 1;
        }

        $position = VkQueue::where('id', '<=', $order->id)->count();

        return $position ?: 1;
    }

    /**
     * Список жанров музыки.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-04
     * @since   1.0
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    function getGenres()
    {
        return ResponseController::success(0, [
            'genres' => trans('vk-audio-genres'),
        ]);
    }
}

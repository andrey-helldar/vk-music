<?php

namespace VKMUSIC\Http\Controllers\Api;

use Illuminate\Http\Request;
use VKMUSIC\Http\Controllers\Controller;
use VKMUSIC\Http\Requests;
use VKMUSIC\VkError;
use VKMUSIC\VkQueue;
use VKMUSIC\VkResponse;

class AudioController extends Controller
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
            'owner_id'   => 'numeric',
        ]);

        if ($validator->fails()) {
            return ResponseController::error(0, $validator->errors()->all());
        }

        return VkController::createRequest('audio.get', array_merge([
            'need_user' => 0,
            'offset'    => (int)($request->offset ?? 0),
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
                break;

            case 'user':
                $owner_id = abs($id);
                break;

            default:
                $owner_id = \Auth::user()->token->user_vk;
        }

        return [
            'owner_id' => $owner_id,
        ];
    }

    /**
     * Возвращает список рекомендуемых аудиозаписей пользователя.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-09
     * @since   1.0
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    function storeRecommendations(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'offset'     => 'numeric|min:0',
            'owner_type' => 'string',
            'owner_id'   => 'string',
        ]);

        if ($validator->fails()) {
            return ResponseController::error(0, $validator->errors()->all());
        }

        return VkController::createRequest('audio.getRecommendations', array_merge([
            'need_user' => 0,
            'offset'    => 0,
            'count'     => config('vk.count_records_force', 50),
        ], $this->ownerId($request->owner_type, $request->owner_id)));
    }

    /**
     * Возврат полученных рекомендуемых аудиозаписей.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-03
     * @since   1.0
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    function getRecommendations()
    {
        $method   = 'audio.getRecommendations';
        $user     = \Auth::user();
        $response = VkResponse::whereUserId($user->id)->whereMethod($method)->where('updated_at', '<', $user->token->expired_at)->first();
        $position = VkController::queuePosition($method, $user->id);

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
    function storePopular(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'genre_id' => 'numeric|min:0|max:100',
            'offset'   => 'numeric|min:0',
        ]);

        if ($validator->fails()) {
            return ResponseController::error(0, $validator->errors()->all());
        }

        return VkController::createRequest('audio.getPopular', [
            'only_eng' => 0,
            'genre_id' => (int)($request->genre_id ?? 0),
            'offset'   => (int)($request->offset ?? 0),
            'count'    => config('vk.count_records_force', 50),
        ]);
    }

    /**
     * Возврат полученных популярных аудиозаписей.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-09
     * @since   1.0
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    function getPopular()
    {
        $method   = 'audio.getPopular';
        $user     = \Auth::user();
        $response = VkResponse::whereUserId($user->id)->whereMethod($method)->where('updated_at', '<', $user->token->expired_at)->first();
        $position = VkController::queuePosition($method, $user->id);

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
            'items'       => $items,
            'count_all'   => count($items),
            'count_query' => config('vk.count_records', 50),
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
        $method   = 'audio.get';
        $user     = \Auth::user();
        $response = VkResponse::whereUserId($user->id)->whereMethod($method)->where('updated_at', '<', $user->token->expired_at)->first();
        $position = VkController::queuePosition($method, $user->id);

        if (is_null($response)) {
            return ResponseController::error(0, [
                'resolve'     => trans('api.21'),
                'description' => trans('api.12', ['position' => $position]),
            ], 406);
        }

        $item = json_decode($response->context);

        if (isset($item->error)) {
            VkError::create([
                'user_id' => $response->user_id,
                'method'  => $response->method,
                'context' => $response->context,
            ]);

            $response->delete();

            return ResponseController::error(0, [
                'resolve'     => $item->error->error_msg,
                'description' => trans('api.12', ['position' => $position]),
            ], 403);
        }

        $item = $item->response;
        $response->delete();

        return ResponseController::success(0, [
            'resolve'     => trans('api.40'),
            'items'       => $item->items,
            'count_all'   => $item->count,
            'count_query' => config('vk.count_records', 50),
        ]);
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

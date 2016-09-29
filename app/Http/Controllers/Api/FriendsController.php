<?php

namespace VKMUSIC\Http\Controllers\Api;

use Illuminate\Http\Request;
use VKMUSIC\Http\Controllers\Controller;
use VKMUSIC\Http\Requests;
use VKMUSIC\VkResponse;

class FriendsController extends Controller
{
    private $method = 'friends.get';

    /**
     * Запрос списка контактов пользователя.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-15
     * @since   1.0
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function storeFriends(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'offset' => 'numeric|min:0',
            'count'  => 'required|integer',
            'fields' => 'string',
        ]);

        if ($validator->fails()) {
            return ResponseController::error(0, $validator->errors()->all());
        }

        return VkController::createRequest($this->method, [
            'offset' => (int)($request->offset ?? 0),
            'count'  => config('vk.count_records', 20),
            'fields' => $request->fields ?? '',
        ]);
    }

    /**
     * Возврат полученного списка контактов пользователя.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-15
     * @since   1.0
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getFriends()
    {
        $user     = \Auth::user();
        $response = VkResponse::whereUserId($user->id)->whereMethod($this->method)->where('updated_at', '<', $user->vk->expired_at)->first();
        $position = VkController::queuePosition($this->method, $user->id);

        if (is_null($response)) {
            return ResponseController::error(0, [
                'resolve'     => trans('api.21'),
                'description' => trans('api.12', ['position' => $position]),
            ], 406);
        }

        $items = json_decode($response->context);

        if (!empty($items->error)) {
            return ResponseController::error(0, [
                'resolve'     => trans('api.1'),
                'description' => trans('api.12', ['position' => $position]),
            ], 406);
        }

        $items = $items->response;
        $response->delete();

        return ResponseController::success(0, [
            'resolve'     => trans('api.40'),
            'items'       => $items->items,
            'count_all'   => $items->count,
            'count_query' => config('vk.count_records', 50),
        ]);
    }
}

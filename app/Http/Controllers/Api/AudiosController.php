<?php

namespace VKMUSIC\Http\Controllers\Api;

use Illuminate\Http\Request;

use VKMUSIC\Http\Requests;
use VKMUSIC\Http\Controllers\Controller;
use VKMUSIC\VkQueue;

class AudiosController extends Controller
{

    public function getUserAudios(Request $request)
    {
        if (\Auth::guest()) {
            return $this->getPopularAudios($request);
        }
    }

    public function getGroupAudios(Request $request)
    {
        return $this->getAudio();
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
    function getPopularAudios(Request $request)
    {
        return VkController::init()->createRequest('audio.getPopular', [
            'only_eng' => 0,
            'genre_id' => (int)($request->genre_id ?: 0),
            'offset'   => (int)($request->offset ?: 0),
            'count'    => 50,
        ]);
    }

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
    private function getAudio(Request $request)
    {
        return VkController::init()->createRequest('audio.get', array_merge([
            'need_user' => 0,
            'offset'    => (int)($request->offset ?: 0),
            'count'     => 50,
        ], $this->checkOwnerId($request->owner_type, $request->owner_id)));
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
    private function checkOwnerId($type, $id)
    {
        switch ($type) {
            case 'group':
                return [
                    'owner_id' => abs($id),
                ];

            case 'user':
                return [
                    'owner_id' => abs($id) * -1,
                ];

            default:
                return [];
        }
    }
}

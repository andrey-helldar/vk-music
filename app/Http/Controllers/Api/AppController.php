<?php

namespace VKMUSIC\Http\Controllers\Api;

use VKMUSIC\Http\Controllers\Controller;
use VKMUSIC\Http\Requests;

class AppController extends Controller
{
    /**
     * Возвращаем список пунктов меню.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-02
     * @since   1.0
     *
     * @return mixed
     */
    public function getTopmenu()
    {
        $menu = [
            [
                'url'       => '/',
                'title'     => 'Player',
                'is_active' => false,
            ],
            [
                'url'       => '/',
                'title'     => 'Components',
                'is_active' => false,
            ],
            [
                'url'       => '/',
                'title'     => 'Javascript',
                'is_active' => false,
            ],
            [
                'url'       => '/',
                'title'     => 'Mobile',
                'is_active' => false,
            ],
        ];

        return ResponseController::success(0, $menu);
    }

    /**
     * Получение параметров для авторизации в ВК.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-02
     * @since   1.0
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getParams()
    {
        return ResponseController::success(0, [
            'client_id'     => config('vk.client_id'),
            'redirect_uri'  => config('vk.redirect_uri'),
            'display'       => config('vk.display'),
            'scope'         => config('vk.scopes'),
            'response_type' => config('vk.response_type'),
            'v'             => config('vk.api_version'),
        ]);
    }
}

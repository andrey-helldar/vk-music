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
}

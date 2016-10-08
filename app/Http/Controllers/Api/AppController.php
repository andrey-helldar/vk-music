<?php

namespace VKMUSIC\Http\Controllers\Api;

use Illuminate\Http\Request;
use VKMUSIC\Feedback;
use VKMUSIC\Http\Controllers\Controller;
use VKMUSIC\Http\Requests;
use VKMUSIC\VkUser;

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
    public function getTopMenu()
    {
        $menu = config('top-menu', []);

        return ResponseController::success(0, array_merge($menu, [
            [
                'is_active' => \Auth::check(),
                'url'       => '/logout',
                'title'     => 'Logout',
                'icon'      => 'send',
            ],
        ]));
    }

    /**
     * Получение блоков для главной страницы.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-28
     * @since   1.0
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getMainBlocks()
    {
        $blocks = config('main-blocks', []);

        return ResponseController::success(0, $blocks);
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
            'response_type' => config('vk.response_type', 'token'),
            'v'             => config('vk.api_version', 5.53),
            'revoke'        => config('vk.revoke', 0),
        ]);
    }

    /**
     * Получение информации о текущем пользователе, записанной в локальной базе.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-15
     * @since   1.0
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getCurrentUserInfo()
    {
        $user_vk = VkUser::whereUserId(\Auth::user()->id)->first();

        if (is_null($user_vk)) {
            return ResponseController::error(trans('api.60'));
        }

        return ResponseController::success(0, [
            'user_vk'         => $user_vk->user_vk,
            'first_name'      => $user_vk->first_name,
            'last_name'       => $user_vk->last_name,
            'first_name_case' => json_decode($user_vk->first_name_case),
            'last_name_case'  => json_decode($user_vk->last_name_case),
            'photo'           => $user_vk->photo,
            'lang'            => $user_vk->lang,
            'is_deactivated'  => $user_vk->is_deactivated,
        ]);
    }

    /**
     * Добавление сообщения через форму обратной связи.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-10-09
     * @since   1.0
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function postFeedback(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email'       => 'required|email',
            'first_name'  => 'required|string|max:255',
            'last_name'   => 'required|string|max:255',
            'description' => 'required|string|max:3000',
        ]);

        if ($validator->fails()) {
            return ResponseController::error(0, $validator->errors()->all());
        }

        Feedback::create([
            'user_id'     => \Auth::check() ? \Auth::user()->id : null,
            'email'       => trim($request->email),
            'first_name'  => trim($request->first_name),
            'last_name'   => trim($request->last_name),
            'description' => e(trim($request->description)),
        ]);

        return ResponseController::success(0, trans('api.70'));
    }
}

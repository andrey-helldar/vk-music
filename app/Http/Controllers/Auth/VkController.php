<?php

namespace VKMUSIC\Http\Controllers\Auth;

use Carbon\Carbon;
use Illuminate\Http\Request;
use VKMUSIC\Http\Controllers\Api\RequestController;
use VKMUSIC\Http\Controllers\Api\ResponseController;
use VKMUSIC\Http\Controllers\Controller;
use VKMUSIC\Http\Requests;
use VKMUSIC\User;
use VKMUSIC\VkUser;


class VkController extends Controller
{

    /**
     * Активация токена доступа для пользователя.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-06
     * @since   1.0
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response|VkController
     */
    public function postVerify(Request $request)
    {
        if ($request->has('access_token')) {
            return $this->verifyAccessToken($request);
        }

        if ($request->has('code')) {
            return $this->verifyCode($request);
        }

        return ResponseController::error(2);
    }

    /**
     * Верификация по токену доступа.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-06
     * @since   1.0
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function verifyAccessToken(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'access_token' => 'required|string',
            'expires_in'   => 'required|numeric',
            'user_id'      => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return ResponseController::error(0, $validator->errors()->all());
        }

        // Проверяем аккаунт юзера. Если не существует - создаем.
        $this->checkUserAccount($request->user_id, $request->access_token, $request->expires_in);

        // Запрашиваем получение расширенной информации об аккаунте пользователя.
        $this->getAccountInfo($request->user_id);

        return ResponseController::success(32);
    }

    /**
     * Проверяем существование юзера в базе.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-02
     * @since   1.0
     *
     * @param $user_vk
     * @param $access_token
     * @param $expires_in
     *
     * @return mixed
     */
    private function checkUserAccount($user_vk, $access_token, $expires_in)
    {
        $user = User::firstOrNew([
            'email' => 'id' . $user_vk . '@vk-music.dev',
        ]);

        $user->name     = 'User ' . $user_vk;
        $user->password = bcrypt($access_token);
        $user->save();

        $vk_user = VkUser::firstOrNew([
            'user_id' => $user->id,
        ]);

        $vk_user->user_vk      = $user_vk;
        $vk_user->access_token = $access_token;
        $vk_user->expired_at   = Carbon::now()->addSeconds($expires_in - 5);
        $vk_user->save();

        // Аутентификация пользователя.
        \Auth::loginUsingId($user->id, true);
    }

    /**
     * Получение информации об аккаунте авторизованного пользователя.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-05
     * @since   1.0
     *
     * @param $user_vk
     *
     * @return bool
     */
    function getAccountInfo($user_vk)
    {
        if (\Auth::guest()) {
            return false;
        }

        \VKMUSIC\Http\Controllers\Api\VkController::createRequest('users.get', [
            'user_ids'  => $user_vk,
            'name_case' => 'nom',
            'fields'    => implode(',', [
                'id',
                'first_name',
                'last_name',
                'first_name_nom',
                'first_name_gen',
                'first_name_dat',
                'first_name_acc',
                'first_name_ins',
                'first_name_abl',
                'last_name_nom',
                'last_name_gen',
                'last_name_dat',
                'last_name_acc',
                'last_name_ins',
                'last_name_abl',
                'photo_100',
                'deactivated',
            ]),
        ]);

        \VKMUSIC\Http\Controllers\Api\VkController::createRequest('account.getInfo', [
            'fields' => implode(',', [
                'lang',
            ]),
        ]);
    }

    /**
     * Верификация по коду.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-06
     * @since   1.0
     *
     * @param Request $request
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function verifyCode(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'code' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return ResponseController::error(0, $validator->errors()->all());
        }

        $response = RequestController::send('POST', 'https://oauth.vk.com/access_token', [
            'client_id'     => config('vk.client_id'),
            'client_secret' => config('vk.client_secret'),
            'redirect_uri'  => config('vk.redirect_uri'),
            'code'          => $request->code,
        ]);

        if (!empty($response->error_description)) {
            return ResponseController::error(0, (array)$response);
        }

        // Проверяем аккаунт юзера. Если не существует - создаем.
        $this->checkUserAccount($response->user_id, $response->access_token, $response->expires_in);

        // Запрашиваем получение расширенной информации об аккаунте пользователя.
        $this->getAccountInfo($response->user_id);

        return ResponseController::success(32);
    }
}

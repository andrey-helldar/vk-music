<?php

namespace VKMUSIC\Http\Controllers\Api;

use VKMUSIC\Http\Controllers\Controller;
use VKMUSIC\Http\Requests;
use VKMUSIC\VkQueue;
use VKMUSIC\VkResponse;

class VkController extends Controller
{
    private static $user = null;

    /**
     * Добавление задания в очередь.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-02
     * @since   1.0
     *
     * @param      $method   Метод, передаваемый VK API.
     * @param      $context  Набор данных для передачи.
     * @param bool $is_alone Проверять ли дублирование записей в базе.
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public static function createRequest($method, $context, $is_alone = true)
    {
        self::checkUser();

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

        $context = array_merge($context, [
            'v'            => config('vk.api_version'),
            'access_token' => self::$user->vk->access_token,
        ]);

        self::sendRequest(self::$user->vk->access_token, $method, $context, $is_alone);

        return ResponseController::success(0, [
            'resolve'     => trans('api.10'),
            'description' => trans('api.12', ['position' => 1]),
        ]);
    }

    /**
     * Проверка существования модели юзера в переменной.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-23
     * @since   1.0
     */
    private static function checkUser()
    {
        if (is_null(self::$user)) {
            self::$user = \Auth::user();
        }
    }

    /**
     * Отправка запросов пользователя на сервер.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-23
     * @since   1.0
     *
     * @param      $access_token
     * @param      $method
     * @param      $context
     * @param bool $is_alone
     */
    private static function sendRequest($access_token, $method, $context, $is_alone = true)
    {
        self::checkUser();

        $response = RequestController::send('POST', 'https://api.vk.com/method/' . $method, $context);

        if (!empty($response->error_description)) {
            self::storeError(self::$user->id, $method, $access_token, $response->error_description, $is_alone);
        } else {
            self::storeSuccess(self::$user->id, $method, $access_token, $response, $is_alone);
        }
    }


    /**
     * Сохранение информации об ошибке.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-03
     * @since   1.0
     *
     * @param      $user_id
     * @param      $method
     * @param      $access_token
     * @param      $resolve
     * @param bool $is_alone
     *
     * @return bool
     */
    private static function storeError($user_id, $method, $access_token, $resolve, $is_alone = true)
    {
        if (!$is_alone) {
            VkResponse::create([
                'user_id'      => $user_id,
                'method'       => $method,
                'access_token' => $access_token,
                'context'      => json_encode([
                    'error' => $resolve,
                ]),
            ]);

            return true;
        }

        $response               = VkResponse::firstOrNew([
            'user_id' => $user_id,
            'method'  => $method,
        ]);
        $response->access_token = $access_token;
        $response->context      = json_encode([
            'error' => $resolve,
        ]);
        $response->save();

        return true;
    }

    /**
     * Сохранение успешной информации.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-03
     * @since   1.0
     *
     * @param      $user_id
     * @param      $method
     * @param      $access_token
     * @param      $response_vk
     * @param bool $is_alone
     *
     * @return bool
     */
    private static function storeSuccess($user_id, $method, $access_token, $response_vk, $is_alone = true)
    {
        if (!$is_alone) {
            VkResponse::create([
                'user_id'      => $user_id,
                'method'       => $method,
                'access_token' => $access_token,
                'context'      => json_encode($response_vk),
            ]);

            return true;
        }

        $response               = VkResponse::firstOrNew([
            'user_id' => $user_id,
            'method'  => $method,
        ]);
        $response->access_token = $access_token;
        $response->context      = json_encode($response_vk);
        $response->save();

        return true;
    }

    /**
     * Считаем позицию запроса пользователя в очереди.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-15
     * @since   1.0
     *
     * @param $method
     * @param $user_id
     *
     * @return int
     */
    public static function queuePosition($method, $user_id)
    {
        $order = VkQueue::whereMethod($method)->whereUserId($user_id)->first();

        if (is_null($order)) {
            return 1;
        }

        $position = VkQueue::where('id', '<=', $order->id)->count();

        return $position ?? 1;
    }
}

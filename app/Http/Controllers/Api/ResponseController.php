<?php

namespace VKMUSIC\Http\Controllers\Api;

use VKMUSIC\Http\Controllers\Controller;
use VKMUSIC\Http\Requests;

class ResponseController extends Controller
{
    /**
     * Возврат успешного ответа.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-02
     * @since   1.0
     *
     * @param int  $code
     * @param null $content
     * @param int  $http_code
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    static function success(int $code = 0, $content = null, $http_code = 200)
    {
        if ($code) {
            $response = self::onCode('success', $code, $content);
        } else {
            $response = self::onText('success', $content);
        }

        return response([
            'response' => $response,
        ], $http_code);
    }

    /**
     * Возврат состояния по коду.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-02
     * @since   1.0
     *
     * @param string $status
     * @param int    $code
     * @param array  $content
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    private static function onCode(string $status = 'success', int $code = 1, $content = [])
    {
        if (is_null($content)) {
            $content = [];
        }

        switch ($status) {
            case 'error':
                return [
                    'error_code' => (int)$code,
                    'error'      => trans('api.' . (string)$code, $content),
                ];

            default:
                return trans('api.' . (string)$code, $content);
        }
    }

    /**
     * Возврат текстовой строки.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-02
     * @since   1.0
     *
     * @param string $status
     * @param        $content
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    private static function onText(string $status = 'success', $content)
    {
        switch ($status) {
            case 'error':
                return [
                    'error' => $content,
                ];

            default:
                return $content;
        }
    }

    /**
     * Основная функция возврата сообщения об ошибках.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-02
     * @since   1.0
     *
     * @param int  $code
     * @param null $content
     * @param int  $http_code
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    static function error(int $code = 0, $content = null, $http_code = 400)
    {
        if ($code) {
            $response = self::onCode('error', $code, $content);
        } else {
            $response = self::onText('error', $content);
        }

        return response($response, $http_code);
    }
}

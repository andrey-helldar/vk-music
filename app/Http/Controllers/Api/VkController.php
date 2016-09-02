<?php

namespace VKMUSIC\Http\Controllers\Api;

use Illuminate\Http\Request;

use Illuminate\Pagination\Paginator;
use VKMUSIC\Http\Requests;
use VKMUSIC\Http\Controllers\Controller;
use VKMUSIC\VkQueue;

class VkController extends Controller
{
    private static $init;

    protected static $access_token = '';
    protected static $api_ver      = 0;

    private function __construct()
    {
        self::$api_ver      = env('VKONTAKTE_API_VERSION', 5.53);
        self::$access_token = \Cookie::get('access_token');
    }

    /**
     * Инициализация класса.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-02
     * @since   1.0
     * @return VkController
     */
    public static function init()
    {
        if (is_null(self::$init)) {
            self::$init = new self();
        }

        return self::$init;
    }

    /**
     * Добавление задания в очередь.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-02
     * @since   1.0
     *
     * @param $method
     * @param $context
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function createRequest($method, $context)
    {
        $validator = \Validator::make([
            //            'method'  => $method,
            'context' => $context,
        ], [
            //            'required|alpha_dash|max:255',
            'array',
        ]);

        if ($validator->fails()) {
            return ResponseController::error(0, $validator->errors()->all());
        }

        // Проверяем уникальность запроса.
        $queue = VkQueue::whereAccessToken(self::$access_token)->whereMethod($method)->first();

        if (!is_null($queue)) {
            return ResponseController::error();
        }

        $context = array_merge($context, [
            'v'            => self::$api_ver,
            'access_token' => self::$access_token,
        ]);

        VkQueue::create([
            'access_token' => self::$access_token,
            'method'       => $method,
            'context'      => json_encode($context),
        ]);

        return ResponseController::success(10);
    }
}

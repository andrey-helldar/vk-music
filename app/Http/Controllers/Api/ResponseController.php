<?php

namespace VKMUSIC\Http\Controllers\Api;

use Illuminate\Http\Request;
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
     * @param $content
     *
     * @return mixed
     */
    static function success($content)
    {
        return response($content);
    }

    /**
     * Возврат ошибки.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-02
     * @since   1.0
     *
     * @param $content
     *
     * @return mixed
     */
    static function error($content)
    {
        return response([
            'error' => $content,
        ], 500);
    }
}

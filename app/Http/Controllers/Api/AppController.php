<?php

namespace VKMUSIC\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Http\Request;
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
            'response_type' => config('vk.response_type', 'token'),
            'v'             => config('vk.api_version', 5.53),
            'revoke'        => config('vk.revoke', 0),
        ]);
    }

    /**
     * Загрузка файлов пользователем с учетом статистики скачиваний.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-06
     * @since   1.0
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function postDownloadFile(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'url'   => 'required|url',
            'title' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return ResponseController::error(0, $validator->errors()->all());
        }

        $url          = mb_substr($request->url, 0, mb_strpos($request->url, '?'));
        $file_content = file_get_contents($url);
        $extension    = pathinfo($url, PATHINFO_EXTENSION);
        $filename     = str_slug(Carbon::now()->micro) . '.' . $extension;

        \Storage::disk('mp3')->put($filename, $file_content);

        return ResponseController::error(0, [
            'url'        => $url,
            'filename'   => $filename,
            'extension'  => $extension,
            'saved_file' => \Storage::disk('mp3')->url($filename),
        ]);

        return response()->download($request->url, $request->title . '.mp3');
    }
}

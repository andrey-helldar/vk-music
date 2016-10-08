<?php

namespace VKMUSIC\Http\Controllers\Api;

use Illuminate\Http\Request;
use VKMUSIC\Http\Controllers\Controller;
use VKMUSIC\Http\Requests;
use VKMUSIC\VkResponse;

class DownloadController extends Controller
{
    private $method = 'audio.getById';

    /**
     * Сохранение данных в очередь на получение ссылки для загрузки файла.
     * Можно, конечно, и сразу через VueJS получить, но, в таком случае,
     * умный юзер может ссылку скопировать, а это нам не надо)
     * Вдобавок, добавим обращения к серверу)
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-10-06
     * @since   1.0
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    function storeFile(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id'       => 'required|numeric',
            'artist'   => 'required|string|max:255',
            'title'    => 'required|string|max:255',
            'duration' => 'required|numeric|min:0',
            'owner_id' => 'required|string|max:255',
            'audios'   => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return ResponseController::error(0, $validator->errors()->all());
        }

        return VkController::createRequest($this->method, $request->all(), false);
    }

    /**
     * Передача скачанных файлов юзеру.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-10-06
     * @since   1.0
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    function getFiles()
    {
        $user  = \Auth::user();
        $files = $user->files;

        if (is_null($files)) {
            return ResponseController::error(0, trans('api.52'), 204);
        }

        $user->files()->delete();
        $items = [];

        foreach ($files as $file) {
            $items[] = [
                'audios' => $file->audios,
                'url'    => route('download', [
                    'id' => $file->file_id,
                ]),
            ];
        }

        return ResponseController::success(0, [
            'resolve' => trans('api.40'),
            'items'   => $items,
        ]);
    }

    /**
     * Считаем позицию запроса пользователя в очереди.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-10-06
     * @since   1.0
     *
     * @param $user_id
     *
     * @return int
     */
    private function getQueuePosition($user_id)
    {
        $order = VkResponse::whereMethod($this->method)->whereUserId($user_id)->first();

        if (is_null($order)) {
            return 1;
        }

        $position = VkResponse::where('id', '<=', $order->id)->count();

        return $position ?: 1;
    }
}

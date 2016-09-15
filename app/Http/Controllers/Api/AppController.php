<?php

namespace VKMUSIC\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use VKMUSIC\Http\Controllers\Controller;
use VKMUSIC\Http\Requests;
use VKMUSIC\VkFile;
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
    public function getTopmenu()
    {
        $menu = [
            [
                'url'       => '#my',
                'api'       => '/api/audio.user',
                'title'     => 'My Audio',
                'is_active' => false,
            ],
            [
                'url'       => '#recommendations',
                'api'       => '/api/audio.recommendations',
                'title'     => 'Recommendations',
                'is_active' => false,
            ],
            [
                'url'       => '#popular',
                'api'       => '/api/audio.popular',
                'title'     => 'Popular',
                'is_active' => false,
            ],
            [
                'url'       => route('friends'),
                'title'     => 'Friends',
                'is_active' => false,
            ],
            [
                'url'       => route('groups'),
                'title'     => 'Groups',
                'is_active' => false,
            ],
        ];

        if (\Auth::check()) {
            $menu[] = [
                'url'       => route('logout'),
                'title'     => 'Logout',
                'is_active' => false,
            ];
        }

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
            'url'      => 'required|url',
            'artist'   => 'required|string|max:255',
            'title'    => 'required|string|max:255',
            'duration' => 'required|numeric|min:0',
            'owner_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return ResponseController::error(0, $validator->errors()->all());
        }

        $disk          = 'mp3';
        $url           = mb_substr($request->url, 0, mb_strpos($request->url, '?'));
        $extension     = pathinfo($url, PATHINFO_EXTENSION);
        $filename_orig = pathinfo($url, PATHINFO_FILENAME);
        $filename      = sprintf("%s-%s-%s.%s", $request->owner_id, $request->duration, $filename_orig, $extension);
        $title         = sprintf("%s - %s.%s", $request->artist, $request->title, $extension);

        if (!\Storage::disk($disk)->exists($filename)) {
            $file_content = file_get_contents($url);
            \Storage::disk($disk)->put($filename, $file_content);
        }

        $file_id = $this->saveFileToDatabase($filename, $title);

        return ResponseController::success(0, [
            'resolve' => trans('api.50', [
                'filename' => $request->artist . ' - ' . $request->title,
            ]),
            'url'     => route('download', ['id' => $file_id]),
            'title'   => $title,
        ]);
    }

    /**
     * Сохраняем запись о файле в базу и возвращаем ее идентификатор.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-07
     * @since   1.0
     *
     * @param $filename
     * @param $title
     *
     * @return mixed
     */
    private function saveFileToDatabase($filename, $title)
    {
        $file = VkFile::withTrashed()->firstOrNew([
            'filename' => $filename,
        ]);

        $file->title      = $title;
        $file->expired_at = Carbon::now()->addMinutes((int)config('vk.files_expired_in', 10));
        $file->deleted_at = null;
        $file->save();

        return $file->id;
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
}

<?php

namespace VKMUSIC\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use VKMUSIC\Download;
use VKMUSIC\VkError;
use VKMUSIC\VkFile;
use VKMUSIC\VkResponse;
use VKMUSIC\VkUser;

class StaticInfoVk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vk:static-info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Static information from VK';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $methods = [
            'account.getInfo',
            'audio.getById',
            'users.get',
        ];
        $items   = VkResponse::whereIn('method', $methods)->get();

        if (!$items->count()) {
            return;
        }

        foreach ($items as $item) {
            switch ($item->method) {
                case 'account.getInfo':
                    $this->accountGetInfo($item);
                    break;

                case 'users.get':
                    $this->usersGet($item);
                    break;

                case 'audio.getById':
                    $this->downloadFile($item);
                    break;

                default:
                    // Все "незнакомые" методы просто пропускаем.
                    continue;
            }

            // Удаляем обработанную запись.
            $item->delete();
        }
    }

    /**
     * Информация об аккаунте пользователя.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-05
     * @since   1.0
     *
     * @param $item
     *
     * @return bool
     */
    private function accountGetInfo($item)
    {
        $user_vk = VkUser::whereUserId($item->user_id)->first();

        if (is_null($user_vk)) {
            return;
        }

        $response = json_decode($item->context);

        if (isset($response->response)) {
            $user_vk->lang = $response->response->lang;
            $user_vk->save();

            return;
        }

        VkError::create([
            'user_id' => $item->user_id,
            'method'  => $item->method,
            'context' => $item->context,
        ]);
    }

    /**
     * Информация о профиле пользователя.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-05
     * @since   1.0
     *
     * @param $item
     *
     * @return bool
     */
    private function usersGet($item)
    {
        $user_vk = VkUser::whereUserId($item->user_id)->first();

        if (is_null($user_vk)) {
            return;
        }

        $item = json_decode($item->context);
        $item = $item->response[0];

        $user_vk->first_name      = $item->first_name;
        $user_vk->last_name       = $item->last_name;
        $user_vk->first_name_case = json_encode([
            'nom' => $item->first_name_nom,
            'gen' => $item->first_name_gen,
            'dat' => $item->first_name_dat,
            'acc' => $item->first_name_acc,
            'ins' => $item->first_name_ins,
            'abl' => $item->first_name_abl,
        ]);
        $user_vk->last_name_case  = json_encode([
            'nom' => $item->last_name_nom,
            'gen' => $item->last_name_gen,
            'dat' => $item->last_name_dat,
            'acc' => $item->last_name_acc,
            'ins' => $item->last_name_ins,
            'abl' => $item->last_name_abl,
        ]);
        $user_vk->photo           = $item->photo_100;
        $user_vk->is_deactivated  = $item->deactivated ?? false;
        $user_vk->save();

        // Обновляем имя юзера на сайте
        $user       = $user_vk->user;
        $user->name = sprintf("%s %s", $user_vk->first_name, $user_vk->last_name);
        $user->save();
    }

    /**
     * Скачивание файла.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-10-06
     * @since   1.0
     *
     * @param $item
     *
     * @return string
     */
    private function downloadFile($item)
    {
        $user_vk = VkUser::whereUserId($item->user_id)->first();

        if (is_null($user_vk)) {
            return;
        }

        $response = json_decode($item->context);

        if (!isset($response->response)) {
            VkError::create([
                'user_id' => $item->user_id,
                'method'  => $item->method,
                'context' => $item->context,
            ]);
        }

        $response = $response->response[0];

        $disk          = 'mp3';
        $url           = mb_substr($response->url, 0, mb_strpos($response->url, '?'));
        $extension     = pathinfo($url, PATHINFO_EXTENSION);
        $filename_orig = pathinfo($url, PATHINFO_FILENAME);
        $filename      = sprintf("%s-%s-%s.%s", $response->owner_id, $response->duration, $filename_orig, $extension);
        $title         = sprintf("%s - %s.%s", $response->artist, $response->title, $extension);

        if (!\Storage::disk($disk)->exists($filename)) {
            try {
                $http = new \GuzzleHttp\Client;
                $file = $http->get($url);
                \Storage::disk($disk)->put($filename, $file->getBody());
            } catch (FatalErrorException $e) {
                return ResponseController::error(0, $e->getMessage());
            }
        }

        $file_id = $this->saveFileToDatabase($filename, $title);

        Download::create([
            'user_id'    => $item->user_id,
            'file_id'    => $file_id,
            'expired_at' => Carbon::now()->addMinutes(30),
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
}

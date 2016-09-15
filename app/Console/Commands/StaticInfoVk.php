<?php

namespace VKMUSIC\Console\Commands;

use Illuminate\Console\Command;
use VKMUSIC\VkError;
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
}

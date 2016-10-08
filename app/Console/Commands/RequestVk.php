<?php

namespace VKMUSIC\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use VKMUSIC\Http\Controllers\Api\RequestController;
use VKMUSIC\VkQueue;
use VKMUSIC\VkResponse;

class RequestVk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vk:request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Requests to VK API.';

    /**
     * Saving log in /storage/logs/laravel.log.
     *
     * @var bool
     */
    protected $save_log = true;

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
        $vkRequestsParams = RequestController::vkRequestsParams();
        $started_at       = Carbon::now();
        $methods          = [
            'qwerty',
        ];
        $items            = VkQueue::whereIn('method', $methods)->take($vkRequestsParams->records)->get();

        $this->log(0, 'RequestVk started at ' . Carbon::now()->format('Y-m-d, H:i:s'));
        $this->log(0, 'Available records: ' . $items->count());
        $this->log(0, 'Acceptable Records Count per Minute: ' . $vkRequestsParams->records);

        foreach ($items as $item) {
            if ($this->isBreakTimeRequest($started_at)) {
                $this->log($item->id, 'isBreakTimeRequest');
                continue;
            }

            if (Carbon::parse($item->user->expired_at) <= Carbon::now()) {
                $this->log($item->id, 'storeError :: Token expired');
                $this->storeError($item->user_id, $item->method, $item->access_token, trans('api.31'));
                $item->delete();
                continue;
            }

            $url = config('vk.request_url', 'http://localhost');

            $response = RequestController::send('POST', str_finish($url, '/') . $item->method, json_decode($item->context));

            if (!empty($response->error_description)) {
                $this->log($item->id, 'storeError :: ' . $response->error_description);
                $this->storeError($item->user_id, $item->method, $item->access_token, $response->error_description);
            } else {
                $this->log($item->id, 'storeSuccess :: ' . gettype($response));
                $this->storeSuccess($item->user_id, $item->method, $item->access_token, $response);
            }

            $item->delete();
        }

        $this->log(0, 'Requesting time: ' . Carbon::now()->diff($started_at)->s . 's');
        $this->log(0, 'RequestVk exiting at ' . Carbon::now()->format('Y-m-d, H:i:s'));
    }

    /**
     * Вывод логов в журнал.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-03
     * @since   1.0
     *
     * @param $id
     * @param $msg
     *
     * @return bool
     */
    private function log($id, $msg)
    {
        if (!$this->save_log) {
            return true;
        }

        $content = sprintf("# %s :: %s", $id, $msg);
        \Log::info($content);
    }

    /**
     * Проверка времени выполнения запросов.
     * При превышении времени возвращаем метку на завершение цикла.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-03
     * @since   1.0
     *
     * @param $started_at
     *
     * @return bool
     */
    private function isBreakTimeRequest($started_at)
    {
        $diff = Carbon::now()->diff($started_at)->s;
        if ($diff > 50) {
            return true;
        }

        return false;
    }

    /**
     * Сохранение информации об ошибке.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-03
     * @since   1.0
     *
     * @param $user_id
     * @param $method
     * @param $access_token
     * @param $resolve
     */
    private function storeError($user_id, $method, $access_token, $resolve)
    {
        $response               = VkResponse::firstOrNew([
            'user_id' => $user_id,
            'method'  => $method,
        ]);
        $response->access_token = $access_token;
        $response->context      = json_encode([
            'error' => $resolve,
        ]);
        $response->save();
    }

    /**
     * Сохранение успешной информации.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-03
     * @since   1.0
     *
     * @param $user_id
     * @param $method
     * @param $access_token
     * @param $response_vk
     */
    private function storeSuccess($user_id, $method, $access_token, $response_vk)
    {
        $response               = VkResponse::firstOrNew([
            'user_id' => $user_id,
            'method'  => $method,
        ]);
        $response->access_token = $access_token;
        $response->context      = json_encode($response_vk);
        $response->save();
    }
}

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
        $acceptableRecordsCount = $this->acceptableRecordsCount();
        $started_at             = Carbon::now();
        $items                  = VkQueue::take($acceptableRecordsCount)->get();
        $this->log(0, 'RequestVk started at ' . Carbon::now()->format('Y-m-d, H:i:s'));
        $this->log(0, 'Available records: ' . $items->count());
        $this->log(0, 'Acceptable Records Count per Minute: ' . $acceptableRecordsCount);

        foreach ($items as $item) {
            if ($this->isBreakTimeRequest($started_at)) {
                $this->log($item->id, 'isBreakTimeRequest');
                break;
            }

            if (Carbon::parse($item->user->expired_at) <= Carbon::now()) {
                $this->log($item->id, 'storeError :: Token expired');
                $this->storeError($item->user_id, $item->method, $item->access_token, trans('api.31'));
                $item->delete();
                continue;
            }

            $response = RequestController::send('POST', 'https://api.vk.com/method/' . $item->method, json_decode($item->context));

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

        return true;
    }

    private function acceptableRecordsCount()
    {
        $delay_requests        = $this->delayRequests();
        $average_response_time = config('vk.average_response_time', 100);

        // Предостерегаемся по времени отвера с сервера - применяем коэффициент 4.
        $average_response_time *= 4;

        // Вычисляем количество запросов в минуту
        $max = 60000 / ($delay_requests + $average_response_time);

        // Возвращаем результат.
        return (int)$max;
    }

    /**
     * Вычисляем в миллисекундах задержку между запросами.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-09
     * @since   1.0
     *
     * @return int
     */
    private function delayRequests()
    {
        $time = 1000 * 1.05;
        $rps  = $time / (int)config('vk.rps', 3);

        return (int)$rps;
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
     * @param $response
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

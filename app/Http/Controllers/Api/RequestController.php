<?php

namespace VKMUSIC\Http\Controllers\Api;

use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use VKMUSIC\Http\Controllers\Controller;
use VKMUSIC\Http\Requests;
use VKMUSIC\ResponseTime;

class RequestController extends Controller
{

    /**
     * Отправка запроса.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-02
     * @since   1.0
     *
     * @param        $method
     * @param string $uri
     * @param array  $formData
     *
     * @return mixed
     */
    public static function send($method, $uri = '/', $formData = [])
    {
        $started_at = 0;
        $stopped_at = 0;

        try {
            $delay_requests = self::vkRequestsParams();
            $http           = new \GuzzleHttp\Client;
            $started_at     = microtime(true);
            $response       = $http->request($method, $uri, [
                'headers'     => [
                    'User-Agent' => env('APP_USER_AGENT', 'AI_RUS/8.0'),
                    'Accept'     => 'application/json',
                ],
                'form_params' => $formData,
                'delay'       => $delay_requests->delay,
            ]);
            $stopped_at     = microtime(true);
            $response       = $response->getBody()->getContents();
        } catch (RequestException $exception) {
            if ($exception->getResponse()) {
                $response = $exception->getResponse()->getBody()->getContents();
            }
        } finally {
            self::calculateAverageRequestTime($started_at, $stopped_at);
        }

        return json_decode($response);
    }

    /**
     * Вычисляем в миллисекундах задержку между запросами.
     *
     * На выходе получаем массив:
     * [
     *     delay       - время ожидания в миллисекундах
     *     delay_micro - время ожидания в микросекундах
     *     rps         - количество запросов в секунду
     *     records     - максимальное количество записей, которое может отработать скрипт в минуту.
     * ]
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-11
     * @since   1.0
     *
     * @return object|mixed
     */
    public static function vkRequestsParams()
    {
        $time = (int)config('vk.cache_delay', 30);

        if (typeOf($time) != 'integer' || $time < 1) {
            $time = 30;
        }

        return \Cache::remember('vkRequestsParams', $time, function () {
            $rps                  = (int)config('vk.rps', 3);
            $response_time_factor = (double)config('vk.request_time_factor', 1.05);
            $time_standard        = 1000 * $response_time_factor;
            $delay                = (int)($time_standard / $rps) + 1;
            $response_time        = ResponseTime::where('created_at', '>', Carbon::now()->addHour(-1))->avg('time');

            // Корректируем время.
            $response_time = abs($response_time ?: 0);

            if ((int)($delay - $response_time) > 0) {
                $delay -= $response_time;
            }

            $delay   = $delay > 0 ? $delay : 0;
            $rps     = $time_standard / ($response_time + $delay);
            $records = 60 * $rps * (double)config('vk.records_factor', 0.9);

            return (object)[
                'delay'       => (int)$delay,
                'delay_micro' => (int)($delay * 1000),
                'rps'         => (int)$rps,
                'records'     => (int)$records,
            ];
        });
    }

    /**
     * Сохранение значения времени каждого ответа от VK API
     *  для последующего вычисления максимально достустимого количества
     *  запросов в минуту.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-10
     * @since   1.0
     *
     * @param $started_at
     * @param $stopped_at
     */
    private static function calculateAverageRequestTime($started_at, $stopped_at)
    {
        $started_at = round($started_at * 1000);
        $stopped_at = round($stopped_at * 1000);

        $equals = (int)($stopped_at - $started_at);

        ResponseTime::create([
            'time' => $equals,
        ]);
    }
}

<?php

namespace VKMUSIC\Http\Controllers\Api;

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
            $http       = new \GuzzleHttp\Client;
            $started_at = microtime(true);
            $response   = $http->request($method, $uri, [
                'headers'     => [
                    'User-Agent' => 'AI RUS/8.0',
                    'Accept'     => 'application/json',
                ],
                'form_params' => $formData,
            ]);
            $stopped_at = microtime(true);
            $response   = $response->getBody()->getContents();
        } catch (RequestException $exception) {
            if ($exception->getResponse()) {
                $response = $exception->getResponse()->getBody()->getContents();
            }
        } finally {
            self::calculateAverageRequestTime($started_at, $stopped_at);
        }

        usleep(self::delayRequests());

        return json_decode($response);
    }

    /**
     * Сохранение значения времени ответа для последующего вычисления.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-09
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

        \Log::alert(sprintf("Started at: %s; Stopped at: %s; Equals: %s", $started_at, $stopped_at, $equals));

        ResponseTime::create([
            'time' => $equals,
        ]);
    }

    /**
     * Таймаут между запросами в микросекундах.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-04
     * @since   1.0
     *
     * @return mixed
     */
    private static function delayRequests()
    {
        $minutes = 60 * 24;

        return \Cache::remember('delayRequests', $minutes, function () {
            $time = 1000 * 1.05;
            $rps  = $time / (int)config('vk.rps', 3);

            return $rps * 1000;
        });
    }
}

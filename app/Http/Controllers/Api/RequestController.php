<?php

namespace VKMUSIC\Http\Controllers\Api;

use GuzzleHttp\Exception\RequestException;
use VKMUSIC\Http\Controllers\Controller;
use VKMUSIC\Http\Requests;

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
        try {
            $http     = new \GuzzleHttp\Client;
            $response = $http->request($method, $uri, [
                'headers'     => [
                    'User-Agent' => 'AI RUS/8.0',
                    'Accept'     => 'application/json',
                ],
                'form_params' => $formData,
            ]);
            $response = $response->getBody()->getContents();
        } catch (RequestException $exception) {
            if ($exception->getResponse()) {
                $response = $exception->getResponse()->getBody()->getContents();
            }
        }

        usleep(config('vk.delay', 350) * 1000);

        return json_decode($response);
    }
}

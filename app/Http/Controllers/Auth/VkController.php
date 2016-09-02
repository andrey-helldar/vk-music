<?php

namespace VKMUSIC\Http\Controllers\Auth;

use Illuminate\Http\Request;

use VKMUSIC\Http\Controllers\Api\ResponseController;
use VKMUSIC\Http\Requests;
use VKMUSIC\Http\Controllers\Controller;

class VkController extends Controller
{
    public function getVerify(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'code' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return abort(400);
        }

        $http    = new \GuzzleHttp\Client();
        $request = new \GuzzleHttp\Psr7\Request('POST', 'https://oauth.vk.com/access_token', ['http_errors' => false]);
        $promise = $http->sendAsync($request)->then(function ($response) {
            dd($response);
        });
        $promise->wait();

        //        $promise = $http->sendAsync([
        //            'client_id'     => config('vk.client_id'),
        //            'client_secret' => config('vk.client_secret'),
        //            'redirect_uri'  => config('vk.redirect_uri'),
        //            'code'          => $request->code,
        //        ])->then(function ($response) {
        //            dd($response);
        //        });
        //        $promise->wait();

        //        $response = json_decode((string)$response->getBody(), true);

        //        dd($response->getBody());
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
            'response_type' => config('vk.response_type'),
            'v'             => config('vk.api_version'),
        ]);
    }
}

<?php

namespace VKMUSIC\Http\Controllers;

use Illuminate\Http\Request;
use VKMUSIC\Http\Controllers\Auth\VkController;
use VKMUSIC\Http\Requests;

class IndexController extends Controller
{
    /**
     * Основная страница.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-05
     * @since   1.0
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex()
    {
        return view('index');
    }

    /**
     * Страница подтверждения доступа.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-06
     * @since   1.0
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getVerify(Request $request)
    {
        if ($request->has('code')) {
            $vkController = new VkController();
            $response     = $vkController->verifyCode($request);
            $content      = json_decode($response->content());
            $statusCode   = $response->getStatusCode();

            if ($statusCode == 200) {
                return redirect()->route('index');
            }
        }

        return view('verify')->withErrors($content->error->error_description);
    }
}

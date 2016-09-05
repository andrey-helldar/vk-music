<?php

namespace VKMUSIC\Http\Controllers;

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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getVerify()
    {
        return view('verify');
    }
}

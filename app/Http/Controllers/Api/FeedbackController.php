<?php

namespace VKMUSIC\Http\Controllers\Api;

use Illuminate\Http\Request;
use VKMUSIC\Http\Controllers\Controller;
use VKMUSIC\Http\Requests;

class FeedbackController extends Controller
{

    /**
     * Получение первоначальных данных для формы обратной связи.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-10-09
     * @since   1.0
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getFeedback()
    {
        if (!\Auth::check()) {
            return ResponseController::success(0, [
                'email'      => '',
                'first_name' => '',
                'last_name'  => '',
            ]);
        }

        $user = \Auth::user();

        return ResponseController::success(0, [
            'email'      => $user->vk->email ?? '',
            'first_name' => $user->vk->first_name,
            'last_name'  => $user->vk->last_name,
        ]);
    }

    /**
     * Добавление сообщения через форму обратной связи.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-10-09
     * @since   1.0
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function postFeedback(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email'       => 'required|email',
            'first_name'  => 'required|string|max:255',
            'last_name'   => 'required|string|max:255',
            'description' => 'required|string|max:3000',
        ]);

        if ($validator->fails()) {
            return ResponseController::error(0, $validator->errors()->all());
        }

        Feedback::create([
            'user_id'     => \Auth::check() ? \Auth::user()->id : null,
            'email'       => trim($request->email),
            'first_name'  => trim($request->first_name),
            'last_name'   => trim($request->last_name),
            'description' => e(trim($request->description)),
        ]);

        return ResponseController::success(0, trans('api.70'));
    }
}

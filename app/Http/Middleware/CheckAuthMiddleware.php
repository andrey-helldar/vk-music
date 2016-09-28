<?php

namespace VKMUSIC\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use VKMUSIC\Http\Controllers\Api\ResponseController;

class CheckAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (\Auth::check()) {
            $expired_at = \Auth::user()->userToken->expired_at;

            if (Carbon::parse($expired_at) <= Carbon::now()) {
                \Auth::logout();
            }
        }

        if (\Auth::guest()) {
            return ResponseController::error(2);
        }

        return $next($request);
    }
}

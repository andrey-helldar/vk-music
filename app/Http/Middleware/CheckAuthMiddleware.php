<?php

namespace VKMUSIC\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

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
            $token_expired_at = \Auth::user()->token->expired_at;

            if (Carbon::parse($token_expired_at) <= Carbon::now()) {
                \Auth::logout();
            }
        }

        if (\Auth::guest()) {
            return ResponseController::error(2);
        }

        return $next($request);
    }
}

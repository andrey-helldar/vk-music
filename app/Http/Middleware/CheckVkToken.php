<?php

namespace VKMUSIC\Http\Middleware;

use Carbon\Carbon;
use Closure;

class CheckVkToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (\Auth::check()) {
            $expired_at = \Auth::user()->token->expired_at;

            if (Carbon::parse($expired_at) <= Carbon::now()) {
                \Auth::logout();
            }
        }

        return $next($request);
    }
}

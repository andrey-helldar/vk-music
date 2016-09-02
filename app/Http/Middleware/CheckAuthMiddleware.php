<?php

namespace VKMUSIC\Http\Middleware;

use Closure;
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
    public function handle($request, Closure $next)
    {
        $access_token = \Cookie::get('access_token');

        if (is_null($access_token)) {
            return ResponseController::error(2);
        }

        return $next($request);
    }
}

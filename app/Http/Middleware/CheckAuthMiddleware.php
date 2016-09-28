<?php

namespace VKMUSIC\Http\Middleware;

use Carbon\Carbon;
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
        $user = $request->user();

        if (!is_null($user)) {
            $expired_at = $user->userToken->expired_at;

            if (Carbon::parse($expired_at) <= Carbon::now()) {
                $user->logout();
            }
        }

        if (is_null($user)) {
            if ($request->ajax() || $request->wantsJson()) {
                return ResponseController::error(2);
            }
        }

        return $next($request);
    }
}

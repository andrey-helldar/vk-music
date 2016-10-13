<?php

namespace VKMUSIC\Http\Middleware;

use Closure;

class LocaleMiddleware
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
        $locale = config('app.locale', 'en');

        if (\Auth::check()) {
            $locale      = \Auth::user()->vk->lang;
            $localeValue = config('vk.lang.' . $locale, null);

            if (!is_null($localeValue)) {
                $locale = $localeValue;
            }
        }

        \Lang::setLocale($locale);

        return $next($request);
    }
}

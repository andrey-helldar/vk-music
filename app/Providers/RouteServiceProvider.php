<?php

namespace VKMUSIC\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'VKMUSIC\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->view->composer('app', function ($view) {

            $user   = [];
            $locale = config('app.locale', 'en');

            if (\Auth::check()) {
                $user_vk     = \Auth::user()->vk;
                $locale      = $user_vk->lang;
                $localeValue = config('vk.lang.' . $locale, null);

                if (!is_null($localeValue)) {
                    $locale = $localeValue;
                }

                $user = [
                    'first_name'      => $user_vk->first_name,
                    'last_name'       => $user_vk->last_name,
                    'first_name_case' => json_decode($user_vk->first_name_case),
                    'last_name_case'  => json_decode($user_vk->last_name_case),
                    'photo'           => $user_vk->photo,
                ];
            }

            \Lang::setLocale($locale);

            $view->with('Laravel', json_encode([
                'csrfToken' => csrf_token(),
                'trans'     => array_dot([
                    'api'             => trans('api'),
                    'validation'      => trans('validation'),
                    'vk-audio-genres' => trans('vk-audio-genres'),
                    'interface'       => trans('interface'),
                    'user'            => $user,
                ]),
            ]));
        });

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapWebRoutes();

        $this->mapApiRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => 'web',
            'namespace'  => $this->namespace,
        ], function ($router) {
            require base_path('routes/web.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::group([
            'middleware' => 'api',
            'namespace'  => $this->namespace,
            'prefix'     => 'api',
        ], function ($router) {
            require base_path('routes/api.php');
        });
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Force HTTPS untuk aset (CSS/JS) saat di lingkungan produksi atau via Ngrok
        if ($this->app->environment('production') || request()->server('HTTP_X_FORWARDED_PROTO') == 'https') {
            URL::forceScheme('https');
        }

        Paginator::useBootstrap();

        // Bagikan variabel unreadCount ke semua view yang menggunakan Auth
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $unreadCount = DB::table('notifications')
                    ->where('user_id', Auth::id())
                    ->where('is_read', false)
                    ->count();

                $view->with('unreadCount', $unreadCount);
            }
        });
    }
}

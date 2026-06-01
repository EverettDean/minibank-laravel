<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;

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

        Paginator::useBootstrap();
    }
}

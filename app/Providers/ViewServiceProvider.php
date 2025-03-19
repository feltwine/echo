<?php

namespace App\Providers;

use App\Models\Hub;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $hubs = auth()->check()
                ? auth()->user()->followedHubs
                : Hub::orderBy('followers_count', 'desc')->take(8)->get();

            $view->with('sidebarHubs', $hubs);
        });
    }

}

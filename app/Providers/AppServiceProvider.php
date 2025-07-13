<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->singleton('pages.data', function () {

            return Cache::rememberForever('pages.json.data', function () {

                $path = storage_path('app/public/json/pages.json');

                if (!File::exists($path)) {
                    return [];
                }

                $json = File::get($path);
                return json_decode($json, true);
            });
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $navigationItems = $this->app->make('pages.data');

        View::share('navigationItems', $navigationItems);
    }
}
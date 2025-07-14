<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\PageController;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $sources = [
            'pages',
            'content',
            'contact'
        ];
        foreach($sources as $source) {
            $this->singleton($source);
        }
    }
    /**
     * Cached file data as singleton
     */
    private function singleton(string $source): void
    {
        $this->app->singleton($source . '.data', function () use ($source) {
            return Cache::rememberForever($source . '.json.data', function () use ($source) {
                $path = storage_path('app/public/json/' . $source . '.json');
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
        View::share('navigationItems', $this->app->make('pages.data'));
        $this->app->when(PageController ::class)
            ->needs('$content')
            ->give($this->app->make('content.data'));
    }
}
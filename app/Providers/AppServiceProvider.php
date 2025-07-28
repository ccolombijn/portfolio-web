<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GeminiController;

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
            'contact',
            'projects'
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
        $pagesData = $this->app->make('pages.data');
        $contentData = $this->app->make('content.data');
        $navigationItems = collect($pagesData)->map(function ($page) {
            $page['routeName'] = isset($page['method'])
                ? $page['name'] . '.' . $page['method']
                : $page['name'];
            return $page;
        })->all();
        $contactData = $this->app->make('contact.data');
        View::share('navigationItems', $navigationItems);
        View::share('contactData', $contactData);
        View::share('content', $contentData);
        $this->app->when(ContactController::class)
            ->needs('$contactData')
            ->give($contactData);

        $this->app->when([
            PageController::class,
            PortfolioController::class,
            ContactController::class,
            GeminiController::class
        ])
            ->needs('$content')
            ->give($contentData);
        $this->app->when(PortfolioController::class)
            ->needs('$projects')
            ->give($this->app->make('projects.data'));
    }
}
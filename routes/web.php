<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ContactController;

foreach (app('pages.data') as $page) {

    $controllerAction = isset($page['controller'])
        ? 'App\\Http\\Controllers\\' . $page['controller'] . '@' . ($page['method'] ?? 'show')
        : [PageController::class, 'show'];

    $routeName = isset($page['method']) 
        ? $page['name'] . '.' . $page['method']
        : $page['name'];

    $routePath = $page['route'] ?? '/' . $page['name'];
    Route::get($routePath, $controllerAction)
        ->defaults('page', $page)
        ->name($routeName);
}

Route::get('/portfolio/{project:slug}', [PortfolioController::class, 'show'])->name('portfolio.show');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
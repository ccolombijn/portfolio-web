<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ContactController;

foreach (app('pages.data') as $page) {
    // Get page controller
    $controllerAction = isset($page['controller'])
        ? 'App\\Http\\Controllers\\' . $page['controller']
        : [PageController::class, 'show'];
    // Define route
    Route::get($page['route'], $controllerAction)
         ->defaults('page', $page)
         ->name($page['name']);
}

Route::get('/portfolio/{project:slug}', [PortfolioController::class, 'show'])->name('portfolio.show');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
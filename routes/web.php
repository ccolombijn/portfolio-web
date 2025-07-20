<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GeminiController;


foreach (collect(app('pages.data'))->all() as $page) {

    $controllerAction = isset($page['controller'])
        ? 'App\\Http\Controllers\\' . $page['controller'] . '@' . ($page['method'] ?? 'show')
        : [PageController::class, 'show'];

    $routeName = isset($page['method'])
        ? $page['name'] . '.' . $page['method']
        : $page['name'];

    $routePath = $page['route'] ?? '/' . $page['name'];

    Route::get($routePath, $controllerAction)
        ->defaults('page', $page)
        ->name($routeName);
}

Route::post('/ai-generate', [GeminiController::class, 'generate'])->name('gemini.generate');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

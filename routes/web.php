<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GeminiController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use Illuminate\Support\Facades\Auth;

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
Auth::routes();
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Page Management (CRUD)
    Route::get('/pages', [AdminPageController::class, 'index'])->name('pages.index');
    Route::get('/pages/create', [AdminPageController::class, 'create'])->name('pages.create');
    Route::post('/pages', [AdminPageController::class, 'store'])->name('pages.store');
    Route::get('/pages/{page}/edit', [AdminPageController::class, 'edit'])->name('pages.edit');
    Route::put('/pages/{page}', [AdminPageController::class, 'update'])->name('pages.update');
    Route::delete('/pages/{page}', [AdminPageController::class, 'destroy'])->name('pages.destroy');
    
    // Project Management (CRUD)
    // You can add these routes similarly using AdminProjectController
    // Route::get('/projects', [AdminProjectController::class, 'index'])->name('projects.index');
});
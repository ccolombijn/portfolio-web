<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FilesController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\AIController;
use Illuminate\Support\Facades\Auth;

foreach (collect(app('pages.data'))->all() as $page) {
    $controllerAction = isset($page['controller'])
        ? 'App\\Http\\Controllers\\' . $page['controller'] . '@' . ($page['method'] ?? 'show')
        : [PageController::class, 'show'];

    $routeName = $page['name'] . (isset($page['method']) && str_contains($page['method'], '.') ? '.' . $page['method'] : '');

    $routePath = $page['route'] ?? '/' . $page['name'];
    Route::get($routePath, $controllerAction)
        ->defaults('page', $page)
        ->name($routeName);
}
// AI Routes
Route::post('/ai-generate', [AIController::class, 'generate'])->name('ai.generate');
Route::get('/ai-profiles', [AIController::class, 'getProfiles'])->name('ai.profiles');
Route::post('/ai-suggest', [AIController::class, 'suggestPrompts'])->name('ai.suggestPrompts');
Route::get('/ai-models', [AIController::class, 'getModels'])->name('ai.models');


// Contact Routes
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
Route::get('/download/{file}', [ContactController::class, 'download'])->name('contact.download');
Auth::routes();
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Page Management
    Route::get('/pages', [AdminPageController::class, 'index'])->name('pages.index');
    Route::get('/pages/create', [AdminPageController::class, 'create'])->name('pages.create');
    Route::post('/pages', [AdminPageController::class, 'store'])->name('pages.store');
    Route::get('/pages/{page}/edit', [AdminPageController::class, 'edit'])->name('pages.edit');
    Route::put('/pages/{page}', [AdminPageController::class, 'update'])->name('pages.update');
    Route::delete('/pages/{page}', [AdminPageController::class, 'destroy'])->name('pages.destroy');

    // Project Management
    Route::get('/projects', [AdminProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [AdminProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [AdminProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}/edit', [AdminProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [AdminProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [AdminProjectController::class, 'destroy'])->name('projects.destroy');

    // File Management
    Route::get('/files', [FilesController::class, 'index'])->name('files.index');
    Route::get('/files/upload/{path?}', [FilesController::class, 'upload'])->name('files.upload')->where('path', '.*');
    Route::post('/files/upload/{path?}', [FilesController::class, 'store'])->name('files.upload.store')->where('path', '.*');
    Route::delete('/files/delete/{path}', [FilesController::class, 'destroy'])->name('files.destroy')->where('path', '.*');
    Route::get('/files/{path}', [FilesController::class, 'index'])->name('files.view')->where('path', '.*');
    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

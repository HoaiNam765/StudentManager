<?php

use App\Http\Controllers\FePreviewController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

// IMPORTANT: include this file from routes/web.php ONLY in local environment.
if (App::environment('local')) {
    Route::get('/__fe-preview', [FePreviewController::class, 'index'])->name('fe.preview.index');

    // Stable named GET routes for every Stitch UI, no business route overwritten.
    foreach (config('fe_preview', []) as $screen) {
        Route::get($screen['path'], function () use ($screen) {
            return app(FePreviewController::class)->show($screen['key']);
        })->name($screen['route']);
    }

    // Compatibility target for inter-screen links in 88 imported mock views.
    Route::get('/__fe-preview/go/{screen}', [FePreviewController::class, 'show'])
        ->where('screen', '(?:admin|student|teacher|public|errors)/[A-Za-z0-9-]+')
        ->name('fe.preview.show');
}

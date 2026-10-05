<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            // Ba cổng giao diện theo docs/BA.md mục 8.1; trang công khai nằm ở routes/web.php
            Route::middleware('web')->prefix('student')->name('student.')
                ->group(base_path('routes/student.php'));
            Route::middleware('web')->prefix('teacher')->name('teacher.')
                ->group(base_path('routes/teacher.php'));
            Route::middleware('web')->prefix('admin')->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

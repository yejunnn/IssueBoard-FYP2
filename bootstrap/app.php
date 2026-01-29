<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'department' => \App\Http\Middleware\DepartmentMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('tickets:delete-cancelled')
            ->daily()
            ->at('02:00')
            ->appendOutputTo(storage_path('logs/ticket-cleanup.log'));
    })->create();

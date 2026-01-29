<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Broadcasting\BroadcastManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Check if Pusher credentials are configured
        $pusherKey = env('PUSHER_APP_KEY');
        $pusherSecret = env('PUSHER_APP_SECRET');
        $pusherAppId = env('PUSHER_APP_ID');
        $broadcastDriver = env('BROADCAST_DRIVER');
        
        // If Pusher credentials are not configured, ensure we use 'log' driver
        if (empty($pusherKey) || empty($pusherSecret) || empty($pusherAppId)) {
            // Force log driver if Pusher credentials are missing
            if (empty($broadcastDriver) || $broadcastDriver === 'pusher') {
                Config::set('broadcasting.default', 'log');
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure Pusher is not used when credentials are missing
        $pusherKey = config('broadcasting.connections.pusher.key');
        $pusherSecret = config('broadcasting.connections.pusher.secret');
        $pusherAppId = config('broadcasting.connections.pusher.app_id');
        
        // If Pusher credentials are empty and default is pusher, switch to log
        if (empty($pusherKey) || empty($pusherSecret) || empty($pusherAppId)) {
            if (config('broadcasting.default') === 'pusher') {
                Config::set('broadcasting.default', 'log');
            }
        }
    }
}

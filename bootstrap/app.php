<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\GlobalSeoMiddleware::class,
        ]);
        
        // Register all middleware aliases in a single call
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'vendor' => \App\Http\Middleware\VendorMiddleware::class,
            'seller' => \App\Http\Middleware\SellerMiddleware::class,
            // Rate limiting configurations
            'throttle.api' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':60,1', // 60 requests per minute
            'throttle.login' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':5,1', // 5 login attempts per minute
            'throttle.admin_login' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':5,1', // 5 admin login attempts per minute
            'throttle.uploads' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':10,1', // 10 uploads per minute
            'throttle.forms' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':30,1', // 30 form submissions per minute
            'throttle.leads' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':10,1', // 10 leads requests per minute
            'throttle.messages' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':5,1', // 5 messages per minute
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

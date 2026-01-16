<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [__DIR__.'/../routes/web.php', __DIR__.'/../routes/auth.php'],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'counselor' => \App\Http\Middleware\CounselorMiddleware::class,
            'counselor_only' => \App\Http\Middleware\CounselorOnlyMiddleware::class,
            'student' => \App\Http\Middleware\StudentMiddleware::class,
            'assistant' => \App\Http\Middleware\AssistantMiddleware::class,
            'counselor_or_assistant' => \App\Http\Middleware\CounselorOrAssistantMiddleware::class,
            'security' => \App\Http\Middleware\SecurityMiddleware::class,
            'feedback_access' => \App\Http\Middleware\FeedbackAccessMiddleware::class,
        ]);
        
        // Security middleware removed for local development
        // Uncomment the line below if you need security features in production
        // $middleware->append(\App\Http\Middleware\SecurityMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

<?php

use App\Http\Middleware\EnsureMembershipIsActive;
use App\Http\Middleware\EnsureUserIsApproved;
use App\Http\Middleware\EnsureUserIsRegular;
use App\Http\Middleware\RedirectAdminFromGuestAuth;
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
        $middleware->alias([
            'user.panel' => EnsureUserIsRegular::class,
            'user.approved' => EnsureUserIsApproved::class,
            'membership.active' => EnsureMembershipIsActive::class,
            'guest.user' => RedirectAdminFromGuestAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

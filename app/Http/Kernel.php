<?php

namespace App\Http;

use App\Http\Middleware\AcquisitionManagerRouteValidate;
use App\Http\Middleware\DispositionManagerRouteValidate;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'admin_route_validate' => \App\Http\Middleware\AdminRouteValidate::class,
        'global_manager_route_validate' => \App\Http\Middleware\GlobalManagerRouteValidate::class,
        'acquisition_manager_route_validate' => AcquisitionManagerRouteValidate::class,
        'disposition_manager_route_validate' => DispositionManagerRouteValidate::class,
        'acquisition_representative_route_validate' => \App\Http\Middleware\AcquisitionRepresentativeRouteValidate::class,
        'cold_caller_route_validate' => \App\Http\Middleware\ColdCallerRouteValidate::class,
        'affiliate_route_validate' => \App\Http\Middleware\AffiliateRouteValidate::class,
        'realtor_route_validate' => \App\Http\Middleware\RealtorRouteValidate::class,
        'disposition_representative_route_validation' => \App\Http\Middleware\DispositionRepresentative::class,
    ];
}
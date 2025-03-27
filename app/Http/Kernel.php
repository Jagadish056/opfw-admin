<?php

namespace App\Http;

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\JWTMiddleware;
use App\Http\Middleware\CloudflareMiddleware;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\LogMiddleware;
use App\Http\Middleware\StaffMiddleware;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Http\Middleware\TrimStrings;
use App\Http\Middleware\TrustProxies;
use App\Http\Middleware\UpdateMiddleware;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ValidateSignature;

class Kernel extends HttpKernel
{

    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Fruitcake\Cors\HandleCors::class,
        UpdateMiddleware::class,
        CloudflareMiddleware::class,
        TrustProxies::class,
        ValidatePostSize::class,
        JWTMiddleware::class,
        TrimStrings::class,
        ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            SubstituteBindings::class,
        ],

        'api' => [
            'throttle:500,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'staff'         => StaffMiddleware::class,
        'log'           => LogMiddleware::class,
        'super-admin'   => SuperAdminMiddleware::class,
        'auth.basic'    => AuthenticateWithBasicAuth::class,
        'bindings'      => SubstituteBindings::class,
        'cache.headers' => SetCacheHeaders::class,
        'signed'        => ValidateSignature::class,
        'throttle'      => ThrottleRequests::class,
        'session'       => JWTMiddleware::class,
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
        JWTMiddleware::class,
        Authenticate::class,
        StaffMiddleware::class,
        SubstituteBindings::class,
        Authorize::class,
    ];

}

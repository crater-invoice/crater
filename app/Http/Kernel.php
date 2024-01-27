<?php

namespace InvoiceShelf\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

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
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \InvoiceShelf\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \InvoiceShelf\Http\Middleware\TrustProxies::class,
        \InvoiceShelf\Http\Middleware\ConfigMiddleware::class,
        \Fruitcake\Cors\HandleCors::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \InvoiceShelf\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \InvoiceShelf\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            EnsureFrontendRequestsAreStateful::class,
            'throttle:180,1',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
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
        'auth' => \InvoiceShelf\Http\Middleware\Authenticate::class,
        'bouncer' => \InvoiceShelf\Http\Middleware\ScopeBouncer::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \InvoiceShelf\Http\Middleware\RedirectIfAuthenticated::class,
        'customer' => \InvoiceShelf\Http\Middleware\CustomerRedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'install' => \InvoiceShelf\Http\Middleware\InstallationMiddleware::class,
        'redirect-if-installed' => \InvoiceShelf\Http\Middleware\RedirectIfInstalled::class,
        'redirect-if-unauthenticated' => \InvoiceShelf\Http\Middleware\RedirectIfUnauthorized::class,
        'customer-guest' => \InvoiceShelf\Http\Middleware\CustomerGuest::class,
        'company' => \InvoiceShelf\Http\Middleware\CompanyMiddleware::class,
        'pdf-auth' => \InvoiceShelf\Http\Middleware\PdfMiddleware::class,
        'cron-job' => \InvoiceShelf\Http\Middleware\CronJobMiddleware::class,
        'customer-portal' => \InvoiceShelf\Http\Middleware\CustomerPortalMiddleware::class,
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces the listed middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \InvoiceShelf\Http\Middleware\Authenticate::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
    ];
}

<?php

use App\Exceptions\Handler;
use App\Http\Middleware\ForceJsonRequestHeader;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
//        api: __DIR__.'/../routes/api.php',  // فقط مسیر فایل اصلی api.php
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (){
            Route::prefix('api')->group(function () {
                Route::middleware('api')
//            ->domain('api.'.env('DOMAIN_URL'))
                    ->prefix('user')
                    ->as('user.')
                    ->group(base_path('routes/api/user.php'));
                Route::middleware('api')
//            ->domain('api.'.env('DOMAIN_URL'))
                    ->prefix('driver')
                    ->as('driver.')
                    ->group(base_path('routes/api/driver.php'));
                Route::middleware('api')
//            ->domain('api.'.env('DOMAIN_URL'))
                    ->prefix('dashboard')
                    ->as('dashboard.')
                    ->group(base_path('routes/api/dashboard.php'));
            });
            },

    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\Authenticate::class);
      //  $middleware->append(ForceJsonRequestHeader::class);
        $middleware->append(\App\Http\Middleware\AllowOnlySpecificIPs::class);
   //     $middleware->append(\App\Http\Middleware\EnsureJsonRequest::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                    'error' => 'Authentication required.'
                ], 401);
            }
        });
        $exceptions->renderable(function (AuthorizationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthorized.',
                    'error' => 'Forbidden.'
                ], 403);
            }
        });
        $exceptions->renderable(function (\Throwable $e, Request $request) {
            if ($e instanceof ValidationException) {
                $errors = $e->errors();
                return error_response('Invalid Params', 400, $errors);
            }
            if ($e instanceof AuthorizationException or $e instanceof AccessDeniedHttpException) {
                return error_response('Forbidden', 403, [
                    'permission' => 'You do not have permission to perform this action'
                ]);
            }
            if ( $request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage() ?: 'Server Error',
                    'error' => 'Something went wrong'
                ], 500);
            }
        });
        $exceptions->report(function (\Throwable $e) {
            try {
                \App\Jobs\SendErrorLog::dispatch([
                    'class_name' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => substr($e->getTraceAsString(), 0, 100)
                ])->onConnection('redis-queue')->onQueue('redis-queue');
            } catch (\Throwable $ex) {
                // اگر خود Job خطا داد، اینجا می‌توانی لاگ بزنی یا نادیده بگیری
//                dd('SendErrorLog failed: ' . $ex->getMessage());
            }
        });

    })
    ->create();

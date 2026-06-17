<?php

use App\Constants\ApiStatuses;
use App\Constants\ErrorMessages;
use App\Http\Middleware\ModuleAccess;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'module.access' => ModuleAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $exception, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $exception->getMessage(),
                    'errors' => $exception->errors(),
                ], ApiStatuses::STATUS_UNPROCESSABLE_ENTITY);
            }
            return false;
        });

        $exceptions->render(function (AuthenticationException $exception, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => ErrorMessages::UNAUTHENTICATED,
                ], ApiStatuses::STATUS_UNAUTHORIZED);
            }
            return false;
        });

        $exceptions->render(function (ModelNotFoundException $exception, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => ErrorMessages::NOT_FOUND,
                ], ApiStatuses::STATUS_NOT_FOUND);
            }
            return false;
        });

        $exceptions->render(function (NotFoundHttpException $exception, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => ErrorMessages::NOT_FOUND,
                ], ApiStatuses::STATUS_NOT_FOUND);
            }
            return false;
        });

        $exceptions->render(function (AccessDeniedHttpException $exception, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => ErrorMessages::FORBIDDEN,
                ], ApiStatuses::STATUS_FORBIDDEN);
            }
            return false;
        });

        $exceptions->render(function (MethodNotAllowedHttpException $exception, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => ErrorMessages::METHOD_NOT_ALLOWED,
                ], ApiStatuses::STATUS_METHOD_NOT_ALLOWED);
            }
            return false;
        });

        $exceptions->render(function (Throwable $exception, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => ErrorMessages::SERVER_ERROR,
                    'error' => $exception->getMessage(),
                ], ApiStatuses::STATUS_INTERNAL_SERVER_ERROR);
            }
            return false;
        });
    })->create();

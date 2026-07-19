<?php

use App\Http\Middleware\CekHakAkses;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->append(HandleCors::class);

        // Register middleware alias untuk digunakan di routes
        $middleware->alias([
            'cek.hak.akses' => CekHakAkses::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Pastikan semua request API selalu mendapat response JSON, bukan HTML
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan.',
                ], 404);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthenticated. Silakan login kembali.',
                ], 401);
            }
        });

        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                return response()->json([
                    'status'  => false,
                    'message' => $e->getMessage() ?: 'Terjadi kesalahan pada server.',
                ], $status);
            }
        });
    })->create();


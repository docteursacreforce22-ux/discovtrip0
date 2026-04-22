<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureUserIsNotBanned;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))

    ->withRouting(
        web:      __DIR__.'/../routes/web.php',
        api:      __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health:   '/up',
    )

    ->withMiddleware(function (Middleware $middleware) {

        // ── Trust Proxies (Railway / Fastly CDN) ──────────────────
        // CRITIQUE : sans ça, les requêtes arrivent en http://
        // → la signature Livewire est générée en https:// mais
        //   vérifiée en http:// → 401 Unauthorized sur upload-file.
        // Kernel.php est ignoré en Laravel 11/12 pour les proxies —
        // seul bootstrap/app.php est pris en compte.
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR      |
                     Request::HEADER_X_FORWARDED_HOST     |
                     Request::HEADER_X_FORWARDED_PORT     |
                     Request::HEADER_X_FORWARDED_PROTO
        );

        // ── Middleware globaux (toutes les requêtes web) ──────────
        $middleware->web(append: [
            \App\Http\Middleware\LocaleMiddleware::class,
        ]);

        // ── Alias utilisables dans les routes ─────────────────────
        $middleware->alias([
            'not.banned' => EnsureUserIsNotBanned::class,
            'role'       => \App\Http\Middleware\CheckRole::class,
            'admin'      => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        // ── Exclusions CSRF ────────────────────────────────────────
        // Le webhook Stripe doit recevoir le payload brut non modifié
        $middleware->validateCsrfTokens(except: [
            'webhooks/stripe',
        ]);

    })

    ->withExceptions(function (Exceptions $exceptions) {

        // ── Page 404 personnalisée ─────────────────────────────────
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if (! $request->expectsJson()) {
                return response()->view('errors.404', [], 404);
            }
        });

        // ── Pages d'erreur HTTP génériques (500, 403, etc.) ────────
        $exceptions->render(function (HttpException $e, Request $request) {
            $status = $e->getStatusCode();
            $view   = "errors.{$status}";

            if (! $request->expectsJson() && view()->exists($view)) {
                return response()->view($view, [], $status);
            }
        });

    })

    ->create();
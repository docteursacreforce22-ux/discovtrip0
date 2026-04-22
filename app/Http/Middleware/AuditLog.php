<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * ════════════════════════════════════════════════════════════════════════════════
 * AUDIT LOGGING MIDDLEWARE
 * ════════════════════════════════════════════════════════════════════════════════
 * Log toutes les actions sensibles pour audit de sécurité:
 * - Authentification
 * - Modifications données
 * - Actions admin
 * - Accès ressources sensibles
 * ════════════════════════════════════════════════════════════════════════════════
 */
class AuditLog
{
    /**
     * Actions à logger
     */
    protected const AUDITABLE_ACTIONS = [
        // Authentication
        'auth.login',
        'auth.logout',
        'auth.register',
        'auth.password.reset',
        '2fa.enable',
        '2fa.disable',
        
        // Admin actions
        'admin.*',
        'user.delete',
        'user.ban',
        'user.role.change',
        
        // Data modifications
        'booking.cancel',
        'payment.refund',
        'review.delete',
        'offer.delete',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        // Exécuter la requête
        $response = $next($request);

        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2); // ms

        // Logger si action auditable
        if ($this->shouldAudit($request, $response)) {
            $this->logAudit($request, $response, $duration);
        }

        return $response;
    }

    /**
     * Déterminer si l'action doit être auditée
     */
    protected function shouldAudit(Request $request, Response $response): bool
    {
        // Logger si activé dans config
        if (!config('security.logging.log_admin_actions', true)) {
            return false;
        }

        // Logger les échecs de login
        if ($request->is('api/auth/login') && $response->getStatusCode() === 401) {
            return true;
        }

        // Logger les actions admin
        if ($request->user()?->role === 'admin') {
            return config('security.logging.log_admin_actions', true);
        }

        // Logger les modifications de données sensibles
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return config('security.logging.log_sensitive_changes', true);
        }

        return false;
    }

    /**
     * Logger l'audit
     */
    protected function logAudit(Request $request, Response $response, float $duration): void
    {
        $user = $request->user();

        $logData = [
            // Request info
            'method' => $request->method(),
            'path' => $request->path(),
            'url' => $request->fullUrl(),
            
            // User info
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'user_role' => $user?->role,
            
            // Client info
            'ip' => TrustProxies::getRealIp($request),
            'user_agent' => $request->userAgent(),
            
            // Response info
            'status' => $response->getStatusCode(),
            'duration_ms' => $duration,
            
            // Metadata
            'timestamp' => now()->toIso8601String(),
        ];

        // Ajouter input data pour certaines actions (sans mots de passe)
        if ($this->shouldLogInput($request)) {
            $input = $request->except([
                'password',
                'password_confirmation',
                'current_password',
                'token',
            ]);
            $logData['input'] = $input;
        }

        // Déterminer le niveau de log
        $level = $this->getLogLevel($response->getStatusCode());

        // Logger
        Log::channel('audit')->log($level, 'Audit log', $logData);
    }

    /**
     * Déterminer si on doit logger les inputs
     */
    protected function shouldLogInput(Request $request): bool
    {
        // Logger inputs pour actions de modification
        return in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']);
    }

    /**
     * Déterminer le niveau de log selon status code
     */
    protected function getLogLevel(int $statusCode): string
    {
        return match (true) {
            $statusCode >= 500 => 'error',
            $statusCode >= 400 => 'warning',
            $statusCode >= 200 && $statusCode < 300 => 'info',
            default => 'debug',
        };
    }

    /**
     * Log une action admin spécifique
     */
    public static function logAdminAction(string $action, array $context = []): void
    {
        Log::channel('audit')->info("Admin action: {$action}", array_merge([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()?->email,
            'ip' => request()->ip(),
            'timestamp' => now()->toIso8601String(),
        ], $context));
    }

    /**
     * Log une tentative de login échouée
     */
    public static function logFailedLogin(string $email, string $ip): void
    {
        if (!config('security.logging.log_failed_logins', true)) {
            return;
        }

        Log::channel('security')->warning('Failed login attempt', [
            'email' => $email,
            'ip' => $ip,
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log une violation de sécurité
     */
    public static function logSecurityViolation(string $type, array $context = []): void
    {
        if (!config('security.logging.log_security_violations', true)) {
            return;
        }

        Log::channel('security')->error("Security violation: {$type}", array_merge([
            'ip' => request()->ip(),
            'path' => request()->path(),
            'user_id' => auth()->id(),
            'timestamp' => now()->toIso8601String(),
        ], $context));
    }
}

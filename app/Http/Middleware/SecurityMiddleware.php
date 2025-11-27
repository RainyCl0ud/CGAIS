<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use App\Services\SecurityService;
use App\Services\AuditService;
use Symfony\Component\HttpFoundation\Response;

class SecurityMiddleware
{
    protected $securityService;
    protected $auditService;

    public function __construct(SecurityService $securityService, AuditService $auditService)
    {
        $this->securityService = $securityService;
        $this->auditService = $auditService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if IP is blacklisted
        if ($this->securityService->isIpBlacklisted($request->ip())) {
            Log::warning('Blocked request from blacklisted IP', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'user_agent' => $request->userAgent(),
            ]);

            return $this->errorResponse($request, 'Access denied', 'Your IP address has been temporarily blocked due to suspicious activity.', 403);
        }

        // Check for suspicious activity
        if ($this->isSuspiciousActivity($request)) {
            $this->securityService->blacklistIp($request->ip(), 3600); // 1 hour

            Log::critical('Suspicious activity detected - IP blacklisted', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'user_agent' => $request->userAgent(),
                'method' => $request->method(),
            ]);

            return response()->json([
                'error' => 'Suspicious activity detected',
                'message' => 'Your access has been temporarily restricted.'
            ], 403);
        }

        // Rate limiting
        if (!$this->checkRateLimit($request)) {
            Log::warning('Rate limit exceeded', [
                'ip' => $request->ip(),
                'user_id' => $request->user()?->id,
                'url' => $request->fullUrl(),
            ]);

            return $this->errorResponse($request, 'Rate limit exceeded', 'Too many requests. Please try again later.', 429);
        }

        // Validate request origin
        if (!$this->validateRequestOrigin($request)) {
            Log::warning('Invalid request origin', [
                'ip' => $request->ip(),
                'origin' => $request->header('Origin'),
                'referer' => $request->header('Referer'),
                'url' => $request->fullUrl(),
            ]);

            return $this->errorResponse($request, 'Invalid request origin', 'Request denied due to invalid origin.', 403);
        }

        // Check for malicious content
        if ($this->containsMaliciousContent($request)) {
            Log::warning('Malicious content detected', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'content_type' => $request->header('Content-Type'),
            ]);

            return $this->errorResponse($request, 'Malicious content detected', 'Request contains potentially harmful content.', 400);
        }

        // Validate CSRF token for state-changing requests
        if ($this->requiresCsrfValidation($request) && !$this->validateCsrfToken($request)) {
            Log::warning('CSRF token validation failed', [
                'ip' => $request->ip(),
                'user_id' => $request->user()?->id,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
            ]);

            return $this->errorResponse($request, 'CSRF token mismatch', 'Security token validation failed.', 419);
        }

        // Log the request for audit
        $this->auditService->logAction('request.received', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'content_length' => $request->header('Content-Length'),
            'content_type' => $request->header('Content-Type'),
        ]);

        // Process the request
        $response = $next($request);

        // Add security headers
        $this->addSecurityHeaders($response);

        // Log the response for audit
        $this->auditService->logAction('request.completed', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'status_code' => $response->getStatusCode(),
            'response_size' => strlen($response->getContent()),
        ]);

        return $response;
    }

    /**
     * Check for suspicious activity
     */
    protected function isSuspiciousActivity(Request $request): bool
    {
        $ip = $request->ip();
        $key = "suspicious_activity:{$ip}";
        $attempts = Cache::get($key, 0);

        // Check for rapid successive requests
        if ($attempts > 50) { // More than 50 requests in 5 minutes
            return true;
        }

        // Check for unusual request patterns
        $unusualPatterns = [
            '/\.\./', // Directory traversal
            '/<script/i', // XSS attempts
            '/union.*select/i', // SQL injection
            '/eval\(/i', // Code injection
            '/base64_decode\(/i', // Encoded payloads
        ];

        $url = $request->fullUrl();
        $userAgent = $request->userAgent();
        $content = $request->getContent();

        foreach ($unusualPatterns as $pattern) {
            if (preg_match($pattern, $url) || 
                preg_match($pattern, $userAgent) || 
                preg_match($pattern, $content)) {
                return true;
            }
        }

        // Increment attempt counter
        Cache::put($key, $attempts + 1, now()->addMinutes(5));

        return false;
    }

    /**
     * Check rate limiting
     */
    protected function checkRateLimit(Request $request): bool
    {
        $identifier = $request->user()?->id ?: $request->ip();
        
        // Different rate limits for different endpoints
        $limits = [
            'api' => 60, // 60 requests per minute for API
            'auth' => 5,  // 5 requests per minute for auth
            'default' => 100, // 100 requests per minute for general
        ];

        $limit = $limits['default'];
        
        if (str_starts_with($request->path(), 'api/')) {
            $limit = $limits['api'];
        } elseif (str_starts_with($request->path(), 'login') || 
                  str_starts_with($request->path(), 'password')) {
            $limit = $limits['auth'];
        }

        return RateLimiter::tooManyAttempts("rate_limit:{$identifier}", $limit);
    }

    /**
     * Validate request origin
     */
    protected function validateRequestOrigin(Request $request): bool
    {
        $allowedOrigins = [
            config('app.url'),
            'https://cgs.example.com',
            'https://www.cgs.example.com',
            'http://localhost:8000', // For development
        ];

        $origin = $request->header('Origin');
        $referer = $request->header('Referer');

        // Allow requests without origin/referer (direct API calls)
        if (!$origin && !$referer) {
            return true;
        }

        if ($origin && !in_array($origin, $allowedOrigins)) {
            return false;
        }

        if ($referer) {
            $refererHost = parse_url($referer, PHP_URL_HOST);
            $allowedHosts = array_map(function($url) {
                return parse_url($url, PHP_URL_HOST);
            }, $allowedOrigins);

            if (!in_array($refererHost, $allowedHosts)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check for malicious content
     */
    protected function containsMaliciousContent(Request $request): bool
    {
        $content = $request->getContent();
        $headers = $request->headers->all();

        $maliciousPatterns = [
            '/<\?php/i',
            '/<script/i',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload/i',
            '/onerror/i',
            '/onclick/i',
            '/eval\(/i',
            '/base64_decode\(/i',
            '/system\(/i',
            '/exec\(/i',
            '/shell_exec\(/i',
            '/passthru\(/i',
            '/`.*`/i', // Backticks for command execution
        ];

        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        // Check headers for suspicious content
        foreach ($headers as $name => $values) {
            foreach ($values as $value) {
                if (preg_match('/<script/i', $value)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if request requires CSRF validation
     */
    protected function requiresCsrfValidation(Request $request): bool
    {
        $stateChangingMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];
        $excludedPaths = [
            'api/',
            'webhook/',
            'callback/',
        ];

        if (!in_array($request->method(), $stateChangingMethods)) {
            return false;
        }

        foreach ($excludedPaths as $path) {
            if (str_starts_with($request->path(), $path)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate CSRF token
     */
    protected function validateCsrfToken(Request $request): bool
    {
        $token = $request->header('X-CSRF-TOKEN') ??
                 $request->input('_token') ??
                 $request->header('X-XSRF-TOKEN');

        if (!$token) {
            return false;
        }

        return hash_equals(session()->token(), $token);
    }

    /**
     * Return appropriate error response based on request type
     */
    protected function errorResponse(Request $request, string $error, string $message, int $statusCode): Response
    {
        if ($this->isApiRequest($request)) {
            return response()->json([
                'error' => $error,
                'message' => $message
            ], $statusCode);
        }

        // For web requests, redirect back with error message
        return redirect()->back()->withErrors([$error => $message])->withInput();
    }

    /**
     * Check if request is an API request
     */
    protected function isApiRequest(Request $request): bool
    {
        return $request->expectsJson() ||
               $request->is('api/*') ||
               str_starts_with($request->path(), 'api/');
    }

    /**
     * Add security headers to response
     */
    protected function addSecurityHeaders(Response $response): void
    {
        $securityHeaders = [
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-Content-Type-Options' => 'nosniff',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self' https:; frame-ancestors 'none';",
            'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains; preload',
            'X-Permitted-Cross-Domain-Policies' => 'none',
            'X-Download-Options' => 'noopen',
            'X-DNS-Prefetch-Control' => 'off',
        ];

        foreach ($securityHeaders as $header => $value) {
            $response->headers->set($header, $value);
        }
    }
}

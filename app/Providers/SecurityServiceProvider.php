<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use App\Policies\UserPolicy;
use App\Policies\AppointmentPolicy;
use App\Policies\FeedbackFormPolicy;
use App\Services\SecurityService;
use App\Services\AuditService;

class SecurityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(SecurityService::class, function ($app) {
            return new SecurityService();
        });

        $this->app->singleton(AuditService::class, function ($app) {
            return new AuditService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
        $this->configurePolicies();
        $this->configureSecurityHeaders();
        $this->configureValidationRules();
        $this->configureBladeDirectives();
        $this->configureEventListeners();
        $this->configureSecurityMiddleware();
    }

    /**
     * Configure rate limiting for security
     */
    protected function configureRateLimiting(): void
    {
        // Login rate limiting
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;
            return Limit::perMinute(5)->by($email.$request->ip())->response(function () {
                Log::warning('Login rate limit exceeded', [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'email' => request()->email
                ]);
                return response()->json([
                    'message' => 'Too many login attempts. Please try again later.'
                ], 429);
            });
        });

        // API rate limiting
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip())->response(function () {
                return response()->json([
                    'message' => 'API rate limit exceeded'
                ], 429);
            });
        });

        // Password reset rate limiting
        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perHour(3)->by($request->ip())->response(function () {
                return response()->json([
                    'message' => 'Too many password reset attempts'
                ], 429);
            });
        });
    }

    /**
     * Configure authorization policies
     */
    protected function configurePolicies(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(\App\Models\Appointment::class, AppointmentPolicy::class);
        Gate::policy(\App\Models\FeedbackForm::class, FeedbackFormPolicy::class);

        // Define additional security gates
        Gate::define('access-admin', function (User $user) {
            return $user->isAdmin() && $user->isActive();
        });

        Gate::define('view-sensitive-data', function (User $user) {
            return $user->hasRole(['counselor', 'assistant', 'admin']) && $user->isActive();
        });

        Gate::define('manage-users', function (User $user) {
            return $user->isAdmin() && $user->isActive();
        });
    }

    /**
     * Configure security headers
     */
    protected function configureSecurityHeaders(): void
    {
        // Security headers will be handled by the SecurityMiddleware
        // This method is kept for future use if needed
    }

    /**
     * Configure custom validation rules
     */
    protected function configureValidationRules(): void
    {
        // Strong password validation
        Validator::extend('strong_password', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $value);
        }, 'The :attribute must be at least 8 characters and contain at least one uppercase letter, one lowercase letter, one number and one special character.');

        // Phone number validation
        Validator::extend('phone_number', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^(\+63|0)9\d{9}$/', $value);
        }, 'The :attribute must be a valid Philippine phone number.');

        // Student ID validation
        Validator::extend('student_id', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^\d{4}-\d{4}$/', $value);
        }, 'The :attribute must be in the format YYYY-XXXX.');

        // No SQL injection validation
        Validator::extend('no_sql_injection', function ($attribute, $value, $parameters, $validator) {
            $dangerousPatterns = [
                '/\b(union|select|insert|update|delete|drop|create|alter|exec|execute|script)\b/i',
                '/[<>"\']/',
                '/javascript:/i',
                '/vbscript:/i',
                '/onload/i',
                '/onerror/i',
                '/onclick/i'
            ];

            foreach ($dangerousPatterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    return false;
                }
            }

            return true;
        }, 'The :attribute contains potentially dangerous content.');
    }

    /**
     * Configure Blade security directives
     */
    protected function configureBladeDirectives(): void
    {
        // CSRF token directive
        Blade::directive('csrf', function () {
            return '<?php echo csrf_field(); ?>';
        });

        // Secure asset directive
        Blade::directive('secureAsset', function ($expression) {
            return "<?php echo secure_asset($expression); ?>";
        });

        // Escape HTML directive
        Blade::directive('escape', function ($expression) {
            return "<?php echo e($expression); ?>";
        });
    }

    /**
     * Configure security event listeners
     */
    protected function configureEventListeners(): void
    {
        // Log failed login attempts
        Event::listen(Failed::class, function ($event) {
            Log::warning('Failed login attempt', [
                'email' => $event->credentials['email'] ?? 'unknown',
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()
            ]);

            // Notify admin of suspicious activity
            if ($this->isSuspiciousActivity(request()->ip())) {
                $this->notifyAdminOfSuspiciousActivity($event);
            }
        });

        // Log successful logins
        Event::listen(Login::class, function ($event) {
            Log::info('User logged in', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()
            ]);

            // Update last login time (temporarily disabled until last_login_at column is added)
            // $event->user->update([
            //     'last_login_at' => now(),
            //     'last_login_ip' => request()->ip()
            // ]);
        });

        // Log logouts
        Event::listen(Logout::class, function ($event) {
            Log::info('User logged out', [
                'user_id' => $event->user->id ?? 'unknown',
                'ip' => request()->ip(),
                'timestamp' => now()
            ]);
        });

        // Log password resets
        Event::listen(PasswordReset::class, function ($event) {
            Log::info('Password reset', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
                'ip' => request()->ip(),
                'timestamp' => now()
            ]);
        });
    }

    /**
     * Configure security middleware
     */
    protected function configureSecurityMiddleware(): void
    {
        // Security middleware is now handled by the SecurityMiddleware class
        // This method is kept for future use if needed
    }

    /**
     * Check for suspicious activity
     */
    protected function isSuspiciousActivity(string $ip): bool
    {
        $key = "suspicious_activity:{$ip}";
        $attempts = cache()->get($key, 0);

        if ($attempts > 10) {
            return true;
        }

        cache()->put($key, $attempts + 1, now()->addMinutes(15));
        return false;
    }

    /**
     * Validate request origin
     */
    protected function isValidOrigin(Request $request): bool
    {
        $allowedOrigins = [
            config('app.url'),
            'https://cgs.example.com',
            'https://www.cgs.example.com'
        ];

        $origin = $request->header('Origin');
        $referer = $request->header('Referer');

        if ($origin && !in_array($origin, $allowedOrigins)) {
            return false;
        }

        if ($referer && !in_array(parse_url($referer, PHP_URL_HOST), array_map(function($url) {
            return parse_url($url, PHP_URL_HOST);
        }, $allowedOrigins))) {
            return false;
        }

        return true;
    }

    /**
     * Notify admin of suspicious activity
     */
    protected function notifyAdminOfSuspiciousActivity($event): void
    {
        // Implementation for admin notification
        // This could send an email, Slack message, or create a security alert
        Log::critical('Suspicious activity detected - Admin notification required', [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'event' => get_class($event)
        ]);
    }
}

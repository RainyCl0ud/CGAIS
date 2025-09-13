<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

class SecurityService
{
    /**
     * Encrypt sensitive data
     */
    public function encryptData($data): string
    {
        try {
            return Crypt::encryptString($data);
        } catch (\Exception $e) {
            Log::error('Encryption failed', [
                'error' => $e->getMessage(),
                'data_length' => strlen($data)
            ]);
            throw new \Exception('Data encryption failed');
        }
    }

    /**
     * Decrypt sensitive data
     */
    public function decryptData($encryptedData): string
    {
        try {
            return Crypt::decryptString($encryptedData);
        } catch (\Exception $e) {
            Log::error('Decryption failed', [
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Data decryption failed');
        }
    }

    /**
     * Hash password with strong algorithm
     */
    public function hashPassword(string $password): string
    {
        return Hash::make($password, [
            'rounds' => 12,
            'memory' => 1024,
            'time' => 2,
            'threads' => 2
        ]);
    }

    /**
     * Verify password
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return Hash::check($password, $hash);
    }

    /**
     * Generate secure random token
     */
    public function generateSecureToken(int $length = 64): string
    {
        return Str::random($length);
    }

    /**
     * Generate secure API key
     */
    public function generateApiKey(): string
    {
        return 'cg_' . Str::random(32) . '_' . time();
    }

    /**
     * Sanitize input data
     */
    public function sanitizeInput($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }

        if (is_string($data)) {
            // Remove potentially dangerous characters
            $data = strip_tags($data);
            $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
            
            // Remove SQL injection patterns
            $dangerousPatterns = [
                '/\b(union|select|insert|update|delete|drop|create|alter|exec|execute)\b/i',
                '/[<>"\']/',
                '/javascript:/i',
                '/vbscript:/i',
                '/onload/i',
                '/onerror/i',
                '/onclick/i'
            ];

            foreach ($dangerousPatterns as $pattern) {
                $data = preg_replace($pattern, '', $data);
            }
        }

        return $data;
    }

    /**
     * Validate and sanitize file upload
     */
    public function validateFileUpload($file, array $allowedTypes = [], int $maxSize = 5242880): array
    {
        $errors = [];

        if (!$file || !$file->isValid()) {
            $errors[] = 'Invalid file upload';
            return $errors;
        }

        // Check file size (default 5MB)
        if ($file->getSize() > $maxSize) {
            $errors[] = 'File size exceeds maximum allowed size';
        }

        // Check file type
        $mimeType = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());

        $allowedMimeTypes = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain'
        ];

        if (!empty($allowedTypes)) {
            $allowedMimeTypes = array_merge($allowedMimeTypes, $allowedTypes);
        }

        if (!in_array($mimeType, $allowedMimeTypes)) {
            $errors[] = 'File type not allowed';
        }

        // Check for malicious content
        if ($this->containsMaliciousContent($file)) {
            $errors[] = 'File contains potentially malicious content';
        }

        return $errors;
    }

    /**
     * Check if file contains malicious content
     */
    protected function containsMaliciousContent($file): bool
    {
        $content = file_get_contents($file->getRealPath());
        
        $maliciousPatterns = [
            '/<\?php/i',
            '/<script/i',
            '/javascript:/i',
            '/vbscript:/i',
            '/eval\(/i',
            '/base64_decode\(/i',
            '/system\(/i',
            '/exec\(/i',
            '/shell_exec\(/i'
        ];

        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate secure filename
     */
    public function generateSecureFilename($originalName): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $filename = Str::random(32) . '_' . time();
        
        return $filename . '.' . $extension;
    }

    /**
     * Validate CSRF token
     */
    public function validateCsrfToken(Request $request): bool
    {
        $token = $request->header('X-CSRF-TOKEN') ?? $request->input('_token');
        
        if (!$token) {
            return false;
        }

        return hash_equals(session()->token(), $token);
    }

    /**
     * Check for brute force attack
     */
    public function isBruteForceAttack(string $identifier, string $type = 'login'): bool
    {
        $key = "brute_force:{$type}:{$identifier}";
        $attempts = Cache::get($key, 0);

        $maxAttempts = [
            'login' => 5,
            'password_reset' => 3,
            'email_verification' => 5
        ];

        return $attempts >= ($maxAttempts[$type] ?? 5);
    }

    /**
     * Record failed attempt
     */
    public function recordFailedAttempt(string $identifier, string $type = 'login'): void
    {
        $key = "brute_force:{$type}:{$identifier}";
        $attempts = Cache::get($key, 0);
        
        Cache::put($key, $attempts + 1, now()->addMinutes(15));
        
        Log::warning("Failed {$type} attempt", [
            'identifier' => $identifier,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'attempts' => $attempts + 1
        ]);
    }

    /**
     * Clear failed attempts
     */
    public function clearFailedAttempts(string $identifier, string $type = 'login'): void
    {
        $key = "brute_force:{$type}:{$identifier}";
        Cache::forget($key);
    }

    /**
     * Validate email format and domain
     */
    public function validateEmail(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $domain = substr(strrchr($email, "@"), 1);
        
        // Check if domain has valid MX record
        if (!checkdnsrr($domain, 'MX')) {
            return false;
        }

        // Check for disposable email domains
        $disposableDomains = [
            'tempmail.org', 'guerrillamail.com', '10minutemail.com',
            'mailinator.com', 'throwaway.email', 'temp-mail.org'
        ];

        if (in_array(strtolower($domain), $disposableDomains)) {
            return false;
        }

        return true;
    }

    /**
     * Validate phone number format
     */
    public function validatePhoneNumber(string $phone): bool
    {
        // Philippine phone number format
        return preg_match('/^(\+63|0)9\d{9}$/', $phone);
    }

    /**
     * Generate secure session ID
     */
    public function generateSecureSessionId(): string
    {
        return Str::random(40);
    }

    /**
     * Check if IP is blacklisted
     */
    public function isIpBlacklisted(string $ip): bool
    {
        $blacklistedIps = Cache::get('blacklisted_ips', []);
        return in_array($ip, $blacklistedIps);
    }

    /**
     * Add IP to blacklist
     */
    public function blacklistIp(string $ip, int $duration = 3600): void
    {
        $blacklistedIps = Cache::get('blacklisted_ips', []);
        $blacklistedIps[] = $ip;
        
        Cache::put('blacklisted_ips', array_unique($blacklistedIps), now()->addSeconds($duration));
        
        Log::warning('IP blacklisted', [
            'ip' => $ip,
            'duration' => $duration,
            'reason' => 'Suspicious activity'
        ]);
    }

    /**
     * Remove IP from blacklist
     */
    public function removeFromBlacklist(string $ip): void
    {
        $blacklistedIps = Cache::get('blacklisted_ips', []);
        $blacklistedIps = array_diff($blacklistedIps, [$ip]);
        
        Cache::put('blacklisted_ips', $blacklistedIps, now()->addDay());
    }

    /**
     * Log security event
     */
    public function logSecurityEvent(string $event, array $data = []): void
    {
        $logData = array_merge($data, [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'user_id' => auth()->id(),
            'timestamp' => now(),
            'url' => request()->fullUrl(),
            'method' => request()->method()
        ]);

        Log::channel('security')->info($event, $logData);
    }

    /**
     * Validate request signature
     */
    public function validateRequestSignature(Request $request, string $secret): bool
    {
        $signature = $request->header('X-Signature');
        $timestamp = $request->header('X-Timestamp');
        $payload = $request->getContent();

        if (!$signature || !$timestamp) {
            return false;
        }

        // Check if timestamp is within 5 minutes
        if (abs(time() - $timestamp) > 300) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $timestamp . $payload, $secret);
        
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Generate request signature
     */
    public function generateRequestSignature(string $payload, string $secret): array
    {
        $timestamp = time();
        $signature = hash_hmac('sha256', $timestamp . $payload, $secret);

        return [
            'signature' => $signature,
            'timestamp' => $timestamp
        ];
    }

    /**
     * Check database connection security
     */
    public function checkDatabaseSecurity(): array
    {
        $issues = [];

        try {
            // Check if database connection is encrypted
            $connection = DB::connection();
            $pdo = $connection->getPdo();
            
            if (!$pdo->getAttribute(\PDO::ATTR_SSL_VERIFY_SERVER_CERT)) {
                $issues[] = 'Database connection is not using SSL';
            }

            // Check for weak passwords in database
            $users = DB::table('users')->where('password', 'like', '%$2y$10$%')->get();
            foreach ($users as $user) {
                if (Hash::needsRehash($user->password)) {
                    $issues[] = "User {$user->email} has weak password hash";
                }
            }

        } catch (\Exception $e) {
            $issues[] = 'Database security check failed: ' . $e->getMessage();
        }

        return $issues;
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Illuminate\Cache\Events\CacheHit;
use Illuminate\Cache\Events\CacheMissed;
use Illuminate\Cache\Events\KeyWritten;
use Illuminate\Cache\Events\KeyForgotten;

class CacheServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->configureCacheEvents();
        $this->configureCacheStrategies();
        $this->configureQueryCaching();
    }

    /**
     * Configure cache event listeners
     */
    protected function configureCacheEvents(): void
    {
        // Log cache hits for monitoring
        Event::listen(CacheHit::class, function ($event) {
            Log::debug('Cache hit', [
                'key' => $event->key,
                'tags' => $event->tags,
            ]);
        });

        // Log cache misses for optimization
        Event::listen(CacheMissed::class, function ($event) {
            Log::debug('Cache miss', [
                'key' => $event->key,
                'tags' => $event->tags,
            ]);
        });

        // Log cache writes
        Event::listen(KeyWritten::class, function ($event) {
            Log::debug('Cache written', [
                'key' => $event->key,
                'tags' => $event->tags,
                'ttl' => $event->seconds,
            ]);
        });

        // Log cache deletions
        Event::listen(KeyForgotten::class, function ($event) {
            Log::debug('Cache forgotten', [
                'key' => $event->key,
                'tags' => $event->tags,
            ]);
        });
    }

    /**
     * Configure cache strategies
     */
    protected function configureCacheStrategies(): void
    {
        // Skip caching during migration to avoid table not found errors
        if (app()->runningInConsole() && isset($_SERVER['argv'][1]) && str_starts_with($_SERVER['argv'][1], 'migrate')) {
            return;
        }

        // Cache frequently accessed data
        $this->cacheFrequentlyAccessedData();
    }

    /**
     * Configure query caching
     */
    protected function configureQueryCaching(): void
    {
        // Cache slow queries
        DB::listen(function ($query) {
            $time = $query->time;
            
            if ($time > 100) { // Log queries taking more than 100ms
                Log::warning('Slow query detected', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $time,
                ]);
            }

            // Cache expensive queries
            if ($time > 500) { // Cache queries taking more than 500ms
                $this->cacheExpensiveQuery($query);
            }
        });
    }

    /**
     * Cache frequently accessed data
     */
    protected function cacheFrequentlyAccessedData(): void
    {
        // Cache user statistics
        Cache::remember('user_stats', 3600, function () {
            return [
                'total_users' => DB::table('users')->count(),
                'active_users' => DB::table('users')->count(), // Temporarily use total users until last_login_at column is added
                'new_users_today' => DB::table('users')->whereDate('created_at', today())->count(),
            ];
        });

        // Cache appointment statistics
        Cache::remember('appointment_stats', 1800, function () { // 30 minutes
            return [
                'total_appointments' => DB::table('appointments')->count(),
                'pending_appointments' => DB::table('appointments')->where('status', 'pending')->count(),
                'today_appointments' => DB::table('appointments')->whereDate('appointment_date', today())->count(),
                'upcoming_appointments' => DB::table('appointments')
                    ->where('appointment_date', '>=', today())
                    ->where('status', 'confirmed')
                    ->count(),
            ];
        });

        // Cache system configuration
        Cache::remember('system_config', 7200, function () { // 2 hours
            return [
                'maintenance_mode' => app()->isDownForMaintenance(),
                'current_version' => config('app.version', '1.0.0'),
                'environment' => config('app.env'),
                'debug_mode' => config('app.debug'),
            ];
        });
    }

    /**
     * Cache expensive queries
     */
    protected function cacheExpensiveQuery($query): void
    {
        $cacheKey = 'query_' . md5($query->sql . serialize($query->bindings));
        
        // Only cache SELECT queries
        if (stripos(trim($query->sql), 'SELECT') === 0) {
            Cache::remember($cacheKey, 300, function () use ($query) { // 5 minutes
                return DB::select($query->sql, $query->bindings);
            });
        }
    }

    /**
     * Clear related caches when data changes
     */
    public static function clearRelatedCaches(string $model, int $id = null): void
    {
        $caches = [
            'user_stats',
            'appointment_stats',
            'system_config',
        ];

        // Clear specific model caches
        if ($model === 'User') {
            $caches[] = "user_{$id}_profile";
            $caches[] = "user_{$id}_appointments";
        } elseif ($model === 'Appointment') {
            $caches[] = "appointment_{$id}_details";
            $caches[] = "counselor_{$id}_schedule";
        }

        foreach ($caches as $cache) {
            Cache::forget($cache);
        }

        Log::info('Cleared related caches', [
            'model' => $model,
            'id' => $id,
            'caches' => $caches,
        ]);
    }

    /**
     * Warm up cache
     */
    public static function warmUpCache(): void
    {
        Log::info('Starting cache warm-up');

        // Warm up user statistics
        Cache::remember('user_stats', 3600, function () {
            return [
                'total_users' => DB::table('users')->count(),
                'active_users' => DB::table('users')->count(), // Temporarily use total users until last_login_at column is added
                'new_users_today' => DB::table('users')->whereDate('created_at', today())->count(),
            ];
        });

        // Warm up appointment statistics
        Cache::remember('appointment_stats', 1800, function () {
            return [
                'total_appointments' => DB::table('appointments')->count(),
                'pending_appointments' => DB::table('appointments')->where('status', 'pending')->count(),
                'today_appointments' => DB::table('appointments')->whereDate('appointment_date', today())->count(),
                'upcoming_appointments' => DB::table('appointments')
                    ->where('appointment_date', '>=', today())
                    ->where('status', 'confirmed')
                    ->count(),
            ];
        });

        // Warm up frequently accessed user data
        $activeUsers = DB::table('users')
            ->pluck('id'); // Temporarily get all users until last_login_at column is added

        foreach ($activeUsers as $userId) {
            Cache::remember("user_{$userId}_profile", 1800, function () use ($userId) {
                return DB::table('users')->where('id', $userId)->first();
            });
        }

        Log::info('Cache warm-up completed', [
            'active_users_cached' => $activeUsers->count(),
        ]);
    }

    /**
     * Get cache statistics
     */
    public static function getCacheStats(): array
    {
        $stats = [
            'total_keys' => 0,
            'memory_usage' => 0,
            'hit_rate' => 0,
            'miss_rate' => 0,
        ];

        // This would require Redis or Memcached specific implementation
        // For now, return basic stats
        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            $redis = Cache::getStore()->getRedis();
            $info = $redis->info();
            
            $stats['total_keys'] = $info['db0']['keys'] ?? 0;
            $stats['memory_usage'] = $info['used_memory'] ?? 0;
        }

        return $stats;
    }

    /**
     * Optimize cache
     */
    public static function optimizeCache(): void
    {
        Log::info('Starting cache optimization');

        // Clear expired keys
        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            $redis = Cache::getStore()->getRedis();
            $redis->flushdb();
        }

        // Rebuild important caches
        self::warmUpCache();

        Log::info('Cache optimization completed');
    }
}

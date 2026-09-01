<?php
namespace App\Core;

/**
 * Enterprise RateLimiter & Anti-Brute-Force Engine
 * Tracks attempt counts and lockouts via secure filesystem storage
 */
class RateLimiter {
    protected static string $storageDir = '';

    protected static function getStorageDir(): string {
        if (!self::$storageDir) {
            $dir = BASE_PATH . '/scratch/rate_limits';
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            self::$storageDir = $dir;
        }
        return self::$storageDir;
    }

    protected static function getFilePath(string $key): string {
        $hash = sha1($key);
        return self::getStorageDir() . '/' . $hash . '.json';
    }

    /**
     * Check if key has exceeded max attempts within the decay window
     */
    public static function tooManyAttempts(string $key, int $maxAttempts, int $decaySeconds = 60): bool {
        $data = self::read($key);
        if (!$data) {
            return false;
        }

        $now = time();
        // Expired window
        if ($now > $data['expires_at']) {
            self::clear($key);
            return false;
        }

        return $data['attempts'] >= $maxAttempts;
    }

    /**
     * Record a hit (attempt) for the key
     */
    public static function hit(string $key, int $decaySeconds = 60): int {
        $data = self::read($key);
        $now = time();

        if (!$data || $now > $data['expires_at']) {
            $data = [
                'attempts' => 1,
                'first_hit_at' => $now,
                'expires_at' => $now + $decaySeconds
            ];
        } else {
            $data['attempts']++;
            // Refresh expiration on consecutive hits if needed
            if ($data['expires_at'] < $now + 5) {
                $data['expires_at'] = $now + $decaySeconds;
            }
        }

        self::write($key, $data);
        return $data['attempts'];
    }

    /**
     * Get remaining lockout seconds
     */
    public static function availableIn(string $key): int {
        $data = self::read($key);
        if (!$data) {
            return 0;
        }

        $remaining = $data['expires_at'] - time();
        return max(0, $remaining);
    }

    /**
     * Clear rate limit records for a key
     */
    public static function clear(string $key): void {
        $file = self::getFilePath($key);
        if (file_exists($file)) {
            @unlink($file);
        }
    }

    /**
     * Internal read
     */
    protected static function read(string $key): ?array {
        $file = self::getFilePath($key);
        if (!file_exists($file)) {
            return null;
        }

        $content = @file_get_contents($file);
        if (!$content) {
            return null;
        }

        $data = json_decode($content, true);
        return is_array($data) ? $data : null;
    }

    /**
     * Internal write
     */
    protected static function write(string $key, array $data): void {
        $file = self::getFilePath($key);
        @file_put_contents($file, json_encode($data), LOCK_EX);
    }
}

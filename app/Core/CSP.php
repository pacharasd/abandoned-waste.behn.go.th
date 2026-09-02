<?php
namespace App\Core;

/**
 * Enterprise Content Security Policy (CSP) Manager
 * Generates cryptographic nonces and builds Mozilla Observatory compliant CSP headers.
 * - Eliminates 'unsafe-inline' and 'data:' from script-src
 * - Restricts object-src to 'none'
 * - Restricts base-uri to 'self'
 * - Whitelists only approved CDN origins
 */
class CSP {
    private static ?string $nonce = null;

    /**
     * Retrieve or generate request-scoped cryptographic nonce (Base64 128-bit random)
     */
    public static function nonce(): string {
        if (self::$nonce === null) {
            self::$nonce = base64_encode(random_bytes(16));
        }
        return self::$nonce;
    }

    /**
     * Helper to render HTML nonce attribute: nonce="..."
     */
    public static function nonceAttr(): string {
        return 'nonce="' . self::nonce() . '"';
    }

    /**
     * Build the strict CSP header string
     */
    public static function getHeader(): string {
        $nonce = self::nonce();
        return "default-src 'self'; "
            . "script-src 'self' 'nonce-{$nonce}' https://cdn.tailwindcss.com https://unpkg.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; "
            . "style-src 'self' 'nonce-{$nonce}' https://fonts.googleapis.com https://unpkg.com; "
            . "font-src 'self' https://fonts.gstatic.com data:; "
            . "img-src 'self' data: blob: https://*.tile.openstreetmap.org https://unpkg.com; "
            . "connect-src 'self'; "
            . "object-src 'none'; "
            . "base-uri 'self'; "
            . "frame-ancestors 'self';";
    }

    /**
     * Send CSP header to response
     */
    public static function sendHeader(): void {
        if (!headers_sent()) {
            header("Content-Security-Policy: " . self::getHeader());
        }
    }
}

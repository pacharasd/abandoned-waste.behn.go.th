<?php
namespace App\Core;

/**
 * PDPA Helper - Privacy & Sensitive Data Masking
 */
class PDPA {
    /**
     * Mask citizen phone number for public display (e.g. 081-234-5678 -> 081-***-5678)
     */
    public static function maskPhone(?string $phone): string {
        if (!$phone) return '-';
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        $len = strlen($cleaned);

        if ($len === 10) {
            return substr($cleaned, 0, 3) . '-***-' . substr($cleaned, 6, 4);
        } elseif ($len === 9) {
            return substr($cleaned, 0, 2) . '-***-' . substr($cleaned, 5, 4);
        }

        return substr($phone, 0, 3) . '***' . substr($phone, -2);
    }

    /**
     * Mask citizen full name for public tracking (e.g. สมศักดิ์ รักสะอาด -> สมศักดิ์ ร.)
     */
    public static function maskName(?string $name): string {
        if (!$name) return '-';
        $parts = explode(' ', trim($name));
        if (count($parts) >= 2) {
            $firstName = $parts[0];
            $lastNameInitial = mb_substr($parts[1], 0, 1, 'UTF-8');
            return $firstName . ' ' . $lastNameInitial . '...';
        }
        return $name;
    }

    /**
     * Clean phone number to digits only
     */
    public static function cleanPhone(?string $phone): string {
        return $phone ? preg_replace('/[^0-9]/', '', $phone) : '';
    }

    /**
     * Check if string is a valid Thai mobile or landline phone number
     */
    public static function isValidThaiPhone(?string $phone): bool {
        if (!$phone) return false;
        $clean = self::cleanPhone($phone);
        return preg_match('/^0[689]\d{8}$/', $clean) === 1 || preg_match('/^0[2-57]\d{7,8}$/', $clean) === 1;
    }

    /**
     * Mask email address for privacy (e.g. somchai@example.com -> s***i@example.com)
     */
    public static function maskEmail(?string $email): string {
        if (!$email || strpos($email, '@') === false) return '-';
        list($username, $domain) = explode('@', $email, 2);
        $len = strlen($username);
        if ($len <= 2) {
            $maskedUser = substr($username, 0, 1) . '***';
        } else {
            $maskedUser = substr($username, 0, 1) . str_repeat('*', min(5, $len - 2)) . substr($username, -1);
        }
        return $maskedUser . '@' . $domain;
    }
}

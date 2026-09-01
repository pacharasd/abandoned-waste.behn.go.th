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
}

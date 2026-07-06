<?php

declare(strict_types=1);

namespace ThreeXUI\Helpers;

class Formatter
{
    /**
     * Convert bytes to human-readable format.
     */
    public static function bytesToHuman(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = (int) min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Convert human-readable bytes to integer bytes.
     */
    public static function humanToBytes(string $value): int
    {
        $value = trim($value);
        $num = (float) $value;

        if ($num === 0.0) {
            return 0;
        }

        $unit = strtoupper(trim(substr($value, -2)));

        return (int) match ($unit) {
            'KB' => $num * 1024,
            'MB' => $num * 1024 * 1024,
            'GB' => $num * 1024 * 1024 * 1024,
            'TB' => $num * 1024 * 1024 * 1024 * 1024,
            'PB' => $num * 1024 * 1024 * 1024 * 1024 * 1024,
            default => (int) $num,
        };
    }

    /**
     * Format a timestamp to ISO 8601.
     */
    public static function timestamp(int $timestamp): string
    {
        return date('c', $timestamp);
    }

    /**
     * Convert GB to bytes.
     */
    public static function gbToBytes(float $gb): int
    {
        return (int) ($gb * 1024 * 1024 * 1024);
    }

    /**
     * Convert bytes to GB.
     */
    public static function bytesToGb(int $bytes): float
    {
        return round($bytes / 1024 / 1024 / 1024, 4);
    }

    /**
     * Format expiry time from milliseconds.
     */
    public static function expiryTime(int $milliseconds): string
    {
        if ($milliseconds === 0) {
            return 'unlimited';
        }

        return self::timestamp((int) ($milliseconds / 1000));
    }

    /**
     * Truncate string to max length with ellipsis.
     */
    public static function truncate(string $text, int $maxLength = 50): string
    {
        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }

        return mb_substr($text, 0, $maxLength - 3) . '...';
    }

    /**
     * Sanitize a string for safe output.
     */
    public static function sanitize(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

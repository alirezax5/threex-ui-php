<?php

declare(strict_types=1);

/**
 * Global helper functions — no namespace so they land in the global scope.
 *
 * Global helper functions for the 3X-UI PHP package.
 */

if (!function_exists('threexui_config')) {
    /**
     * Create a new 3X-UI client instance with optional API token.
     *
     * @param string      $panelUrl The base URL of the 3X-UI panel
     * @param string|null $apiToken Optional API token from panel settings
     * @return \ThreeXUI\ThreeXUI
     */
    function threexui_client(string $panelUrl, ?string $apiToken = null): \ThreeXUI\ThreeXUI
    {
        $client = new \ThreeXUI\ThreeXUI($panelUrl);

        if ($apiToken !== null) {
            $client->withApiToken($apiToken);
        }

        return $client;
    }
}

if (!function_exists('bytes_to_human')) {
    /**
     * Convert bytes to human-readable format.
     */
    function bytes_to_human(int $bytes, int $precision = 2): string
    {
        return \ThreeXUI\Helpers\Formatter::bytesToHuman($bytes, $precision);
    }
}

if (!function_exists('human_to_bytes')) {
    /**
     * Convert human-readable size string to bytes.
     */
    function human_to_bytes(string $value): int
    {
        return \ThreeXUI\Helpers\Formatter::humanToBytes($value);
    }
}

if (!function_exists('gb_to_bytes')) {
    /**
     * Convert gigabytes to bytes.
     */
    function gb_to_bytes(float $gb): int
    {
        return \ThreeXUI\Helpers\Formatter::gbToBytes($gb);
    }
}

if (!function_exists('bytes_to_gb')) {
    /**
     * Convert bytes to gigabytes.
     */
    function bytes_to_gb(int $bytes): float
    {
        return \ThreeXUI\Helpers\Formatter::bytesToGb($bytes);
    }
}

if (!function_exists('array_dot_get')) {
    /**
     * Get value from array using dot notation.
     */
    function array_dot_get(array $array, string $key, mixed $default = null): mixed
    {
        return \ThreeXUI\Helpers\ArrayHelper::dotGet($array, $key, $default);
    }
}

if (!function_exists('validate_uuid')) {
    /**
     * Check if a string is a valid UUID.
     */
    function validate_uuid(string $value): bool
    {
        return \ThreeXUI\Helpers\Validator::isUuid($value);
    }
}

if (!function_exists('validate_protocol')) {
    /**
     * Check if a string is a valid Xray protocol.
     */
    function validate_protocol(string $protocol): bool
    {
        return \ThreeXUI\Helpers\Validator::isValidProtocol($protocol);
    }
}

if (!function_exists('validate_port')) {
    /**
     * Check if a value is a valid port number (1-65535).
     */
    function validate_port(mixed $port): bool
    {
        return \ThreeXUI\Helpers\Validator::isValidPort($port);
    }
}

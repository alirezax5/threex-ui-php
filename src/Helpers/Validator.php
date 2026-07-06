<?php

declare(strict_types=1);

namespace ThreeXUI\Helpers;

use ThreeXUI\Exceptions\ValidationException;

class Validator
{
    /**
     * Validate required fields in an array.
     *
     * @throws ValidationException
     */
    public static function requiredFields(array $data, array $fields): void
    {
        foreach ($fields as $field) {
            if (!array_key_exists($field, $data)) {
                throw new ValidationException("Missing required field: {$field}");
            }

            if ($data[$field] === null || $data[$field] === '') {
                throw new ValidationException("Field '{$field}' cannot be empty.");
            }
        }
    }

    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function assertEmail(string $email): void
    {
        if (!self::isValidEmail($email)) {
            throw new ValidationException("Invalid email address: {$email}");
        }
    }

    public static function isValidPort(mixed $port): bool
    {
        return is_int($port) && $port >= 1 && $port <= 65535;
    }

    public static function assertPort(mixed $port): void
    {
        if (!self::isValidPort($port)) {
            throw new ValidationException("Invalid port number: {$port}. Must be between 1 and 65535.");
        }
    }

    public static function isValidProtocol(string $protocol): bool
    {
        $valid = ['vless', 'vmess', 'trojan', 'shadowsocks', 'dokodemo-door', 'socks', 'http'];

        return in_array(strtolower($protocol), $valid, true);
    }

    public static function assertProtocol(string $protocol): void
    {
        if (!self::isValidProtocol($protocol)) {
            throw new ValidationException("Invalid protocol: {$protocol}");
        }
    }

    public static function isPositiveInt(mixed $value): bool
    {
        return is_int($value) && $value >= 0;
    }

    public static function assertPositiveInt(mixed $value, string $fieldName = 'value'): void
    {
        if (!self::isPositiveInt($value)) {
            throw new ValidationException("{$fieldName} must be a positive integer.");
        }
    }

    public static function isUuid(string $value): bool
    {
        return (bool) preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $value);
    }

    public static function assertUuid(string $value): void
    {
        if (!self::isUuid($value)) {
            throw new ValidationException("Invalid UUID format: {$value}");
        }
    }

    public static function nonEmptyArray(string $field, mixed $value): void
    {
        if (!is_array($value) || $value === []) {
            throw new ValidationException("{$field} must be a non-empty array.");
        }
    }
}

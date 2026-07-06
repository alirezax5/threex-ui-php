<?php

declare(strict_types=1);

namespace ThreeXUI\Helpers;

class ArrayHelper
{
    /**
     * Get value using dot notation: 'inbound.settings.clients'.
     */
    public static function dotGet(array $array, string $key, mixed $default = null): mixed
    {
        if ($key === '') {
            return $array;
        }

        $keys = explode('.', $key);

        foreach ($keys as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }
            $array = $array[$segment];
        }

        return $array;
    }

    /**
     * Set value using dot notation.
     */
    public static function dotSet(array &$array, string $key, mixed $value): void
    {
        $keys = explode('.', $key);
        $current = &$array;

        foreach ($keys as $i => $segment) {
            if ($i === count($keys) - 1) {
                $current[$segment] = $value;
            } else {
                if (!isset($current[$segment]) || !is_array($current[$segment])) {
                    $current[$segment] = [];
                }
                $current = &$current[$segment];
            }
        }
    }

    /**
     * Check if key exists using dot notation.
     */
    public static function dotHas(array $array, string $key): bool
    {
        $keys = explode('.', $key);

        foreach ($keys as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return false;
            }
            $array = $array[$segment];
        }

        return true;
    }

    /**
     * Remove null and empty values recursively.
     */
    public static function removeEmpty(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = self::removeEmpty($value);

                if ($array[$key] === []) {
                    unset($array[$key]);
                }
            } elseif ($value === null || $value === '' || $value === []) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    /**
     * Convert array to query string.
     */
    public static function toQuery(array $params): string
    {
        return http_build_query(self::removeEmpty($params));
    }

    /**
     * Return only specified keys.
     */
    public static function only(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }

    /**
     * Return all keys except specified ones.
     */
    public static function except(array $array, array $keys): array
    {
        return array_diff_key($array, array_flip($keys));
    }
}

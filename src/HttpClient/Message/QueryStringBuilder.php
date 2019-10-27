<?php

declare(strict_types=1);

namespace Gitlab\HttpClient\Message;

use function array_filter;
use function array_keys;
use function array_map;
use function count;
use function implode;
use function is_array;
use function range;
use function rawurlencode;

final class QueryStringBuilder
{
    /**
     * Encode a query as a query string according to RFC 3986. Indexed arrays are encoded using
     * empty squared brackets ([]) unlike http_build_query.
     *
     * @param string[]|string $query
     */
    public static function build($query): string
    {
        if (! is_array($query)) {
            return static::rawurlencode($query);
        }
        $query = array_filter($query, static function ($value) {
            return $value !== null;
        });

        return implode('&', array_map(static function ($value, $key) {
            return static::encode($value, $key);
        }, $query, array_keys($query)));
    }

    /**
     * Encode a value
     *
     * @param mixed $query
     */
    private static function encode($query, string $prefix): string
    {
        if (! is_array($query)) {
            return static::rawurlencode($prefix) . '=' . static::rawurlencode((string)$query);
        }

        $isIndexedArray = static::isIndexedArray($query);

        return implode('&', array_map(static function ($value, $key) use ($prefix, $isIndexedArray) {
            $prefix = $isIndexedArray ? $prefix . '[]' : $prefix . '[' . $key . ']';

            return static::encode($value, $prefix);
        }, $query, array_keys($query)));
    }

    /**
     * Tell if the given array is an indexed one (i.e. contains only sequential integer keys starting from 0).
     *
     * @param mixed[] $query
     */
    public static function isIndexedArray(array $query): bool
    {
        if (empty($query) || ! isset($query[0])) {
            return false;
        }

        return array_keys($query) === range(0, count($query) - 1);
    }

    /**
     * Encode a value like rawurlencode, but return "0" when false is given.
     */
    private static function rawurlencode(string $value): string
    {
        if ($value === '') {
            return '0';
        }

        return rawurlencode($value);
    }
}

<?php

namespace Rosem\Component\String;

use function ctype_lower;
use function preg_replace;
use function strtolower;

/**
 * Convert a string to camelCase.
 *
 * @param string $value
 *
 * @return string
 */
function camelCase(string $value): string
{
    return lcfirst(studlyCase($value));
}

/**
 * Convert a string to snake_case.
 *
 * @param  string $value
 * @param  string $delimiter
 *
 * @return string
 */
function snakeCase(string $value, string $delimiter = '_'): string
{
    if (!ctype_lower($value)) {
        return strtolower(preg_replace('/(.)(?=[A-Z])/', '$1' . $delimiter, $value));
    }

    return $value;
}

/**
 * Convert a string to StudlyCase.
 *
 * @param string $value
 *
 * @return string
 */
function studlyCase(string $value): string
{
    return str_replace(
        ' ',
        '',
        ucwords(trim(preg_replace('/[^a-z0-9]+/i', ' ', $value)))
    );
}

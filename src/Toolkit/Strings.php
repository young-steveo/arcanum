<?php

declare(strict_types=1);

namespace Arcanum\Toolkit;

use voku\helper\ASCII;

/**
 * Strings
 * -------
 *
 * A collection of methods for working with strings.
 */
final class Strings
{
    /**
     * Convert a string to ASCII.
     *
     * @param ASCII::*_LANGUAGE_CODE $language
     */
    public static function ascii(string $string, string $language = 'en'): string
    {
        return ASCII::to_ascii($string, $language);
    }

    /**
     * Convert a string to camel case.
     */
    public static function camel(string $string): string
    {
        return lcfirst(static::pascal($string));
    }

    /**
     * Convert a string to kebab case.
     */
    public static function kebab(string $string): string
    {
        return static::linked($string, '-');
    }

    /**
     * Convert a string to all lower case characters, where whitespace is
     * replaced with the given delimiter.
     */
    public static function linked(string $string, string $delimiter): string
    {
        if (ctype_lower($string)) {
            return $string;
        }

        $string = preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, static::pascal($string));

        return strtolower((string)$string);
    }

    /**
     * Convert a string to pascal case.
     */
    public static function pascal(string $string): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $string)));
    }

    /**
     * Convert a string to snake case.
     */
    public static function snake(string $string): string
    {
        return static::linked($string, '_');
    }

    /**
     * Convert a string to title case.
     */
    public static function title(string $string): string
    {
        return mb_convert_case($string, \MB_CASE_TITLE, 'UTF-8');
    }
}

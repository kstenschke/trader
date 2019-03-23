<?php

namespace helpers;

/**
 * Public static helper functions for not applications specific context: Strings
 */
class Strings {

    /**
     * @param  string $haystack
     * @param  string $needle
     * @return bool
     */
    public static function startsWith($haystack, $needle): bool
    {
        return $needle === '' || strpos($haystack, $needle) === 0;
    }

    /**
     * @param  string $haystack
     * @param  string $needle
     * @return bool
     */
    public static function endsWith($haystack, $needle): bool
    {
        return $needle === '' || substr($haystack, -strlen($needle)) === $needle;
    }

    /**
     * @param  string $str
     * @return bool
     */
    public static function endsNumeric($str): bool
    {
        return is_numeric(substr($str, -1));
    }

    /**
     * @param  string $str
     * @return string
     */
    public static function removeTrailingNumbers($str): string
    {
        while (self::endsNumeric($str)) {
            $str = substr($str, 0, -1);
        }

        return $str;
    }

    /**
     * Replace 1st occurrence of given sub-string
     *
     * @param  String $search   string to be replaced
     * @param  String $replace  string to be used for replacement
     * @param  String $string   source string to do the replacement inside
     * @return String  the string with the replacement done
     */
    public static function replaceFirst($string, $search, $replace = ''): string
    {
        if (strlen($search) > 0) {
            $pos = strpos($string, $search);
            if (is_int($pos)) {
                $len = strlen($search);

                return substr_replace($string, $replace, $pos, $len);
            }
        }

        return $string;
    }
}

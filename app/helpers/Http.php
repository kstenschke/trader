<?php

namespace helpers;

/**
 * Public static helper functions for not applications specific context: HTTP
 */
class Http {

    /**
     * Redirect to given destination
     *
     * @param string $destination
     */
    public static function redirect($destination): void
    {
        if (self::isUrl($destination)) {
            header('Location: ' . $destination);
        } elseif (self::isAbsolutePath($destination)) {
            // Handle absolute path
            $protocol = @$_SERVER['HTTPS'] ? 'https' : 'http';
            $host     = $_SERVER['HTTP_HOST'];
            header("Location: $protocol://$host$destination");
        } else {
            // Handle relative path
            $protocol = @$_SERVER['HTTPS'] ? 'https' : 'http';
            $host     = $_SERVER['HTTP_HOST'];
            $path     = rtrim(dirname($_SERVER['PHP_SELF']), "/\\");

            header("Location: $protocol://$host$path/$destination");
        }
        exit;
    }

    /**
     * @param  string $httpPath
     * @return bool
     */
    private static function isUrl($httpPath): bool
    {
        return preg_match("/^http[s]*:\/\//", $httpPath) > 0;
    }

    /**
     * @param  string $httpPath
     * @return int
     */
    private static function isAbsolutePath($httpPath): int
    {
        return preg_match("/^\//", $httpPath);
    }
}

<?php

/**
 * Custom error handler
 *
 * @param int    $code
 * @param string $message
 * @param string $file
 * @param int    $line
 * @return bool|null
 */
function errorHandler($code, $message, $file, $line) {
    if ($code === E_USER_ERROR) {
        $handle = \fopen(PATH_ROOT . '/var/logs/error.log', 'ab');
        if (false !== $handle) {
            \fwrite($handle,
                \date('d.m.y h:i:s') . ' - code ' . $code . ":\n" .
                '    ' . $message . "\n" .
                '    ' . 'in ' . $file . ' / line ' . $line . "\n"
            );
            \fclose($handle);
        }

        die (include PATH_APP . '/views/error.phtml');
    }

    // Pass error to PHP default error handler
    return null;
}

<?php

namespace helpers;

/**
 * Public static helper functions for not applications specific context: Files
 */
class Files {

    /**
     * Get list of files in given directory, optional recursive, optionally filtered: only files w/ filename containing the given substring
     *
     * @param  string $path
     * @param  string $ext        Optional: limit to file extension or substring
     * @param  bool   $recursive
     * @param  string $leadString Optional: limit to files beginning w/ this string
     * @param  bool   $namesOnly
     * @return array|false          Matched files (including their full path), or FALSE if path invalid
     */
    public static function scanDir($path, $ext = '', $recursive = false, $leadString = '', $namesOnly = false)
    {
        if (!is_dir($path)) {
            return [];
        }
        if ($recursive) {
            return self::scanDirRecursive($path, $ext, $leadString);
        }

        $files = scandir($path, SORT_DESC);

        // Filter: 1. remove '.' and '..', 2. by extension (or substring), 3. by lead string
        $filesFiltered = [];
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && self::hasMatchingExtension($file, $ext) && self::basenameStartsWith($file, $leadString)) {
                $filesFiltered[] = $file;
            }
        }

        if (!$namesOnly) {
            // Prepend files w/ path
            foreach ($filesFiltered as &$fileFiltered) {
                $fileFiltered = $path . DIRECTORY_SEPARATOR . $fileFiltered;
            }
        }

        return $filesFiltered;
    }

    /**
     * @param  string $path
     * @param  string $extension
     * @param  string $leadString Optional: limit to files beginning w/ this string
     * @return array                Filenames including the full path
     */
    public static function scanDirRecursive($path, $extension = '', $leadString = ''): array
    {
        $items = [];
        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {
                $pathFile = $path . DIRECTORY_SEPARATOR . $file;
                if (preg_match('/^(^\.)/', $file) === 0) {
                    if (is_dir($pathFile)) {
                        $items = array_merge($items, self::scanDirRecursive($pathFile, $extension, $leadString));
                    } elseif (self::hasMatchingExtension($pathFile, $extension) && self::basenameStartsWith($pathFile, $leadString)) {
                        $items[] = preg_replace('/\/\//', DIRECTORY_SEPARATOR, $pathFile);
                    }
                }
            }

            closedir($handle);
        }

        return $items;
    }

    /**
     * @param string $pathFile
     * @param string $content
     */
    public static function write($pathFile, $content)
    {
        $handle = fopen($pathFile, 'wb');
        fwrite($handle, $content);
        fclose($handle);
    }

    /**
     * @param  string $filename
     * @param  string $extension
     * @return bool
     */
    private static function hasMatchingExtension($filename, $extension): bool
    {
        return empty($extension) || strpos($filename, $extension) !== false;
    }

    /**
     * @param  string $filename
     * @param  string $leadString
     * @return bool
     */
    private static function basenameStartsWith($filename, $leadString): bool
    {
        return empty($leadString) || Strings::startsWith(basename($filename), $leadString);
    }
}

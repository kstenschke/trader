<?php

namespace models;

class Config
{
    /** @var array */
    private static $config;

    /**
     * @param  string $key
     * @return array|string
     * @singleton
     */
    public static function getConfig(string $key = null)
    {
        if (null === self::$config) {
            $pathConfig = PATH_APP . DIRECTORY_SEPARATOR . 'config.json';
            self::$config = self::loadJsonConfig($pathConfig);
        }

        return $key === null ? self::$config : self::$config[$key];
    }

    public static function isJson(string $string): bool
    {
        /** @noinspection ReturnNullInspection */
        \json_decode($string);

        return \json_last_error() === JSON_ERROR_NONE;
    }

    protected static function loadJsonConfig(string $pathConfig)
    {
        if (!\file_exists($pathConfig)) {
            \trigger_error("Config not found: $pathConfig", E_USER_ERROR);
        }

        $json = \file_get_contents($pathConfig);
        if (!self::isJson($json)) {
            \trigger_error("Config is NOT valid JSON: $pathConfig", E_USER_ERROR);
        }

        return \json_decode($json, true);
    }
}

<?php

namespace models;

class Scraper
{
    /**
     * Pulls stock price, name etc.
     *
     * @param  string $symbol
     * @return array|bool
     */
    public static function getCurrentData(string $symbol)
    {
        /** @var string|ScraperIexCloud $model */
        $model = Config::getConfig('scraper')['model'];
        require_once PATH_APP . '/models/' . $model . '.php';

        return \call_user_func('models\\' . $model . '::getCurrentData', $symbol);
    }
}

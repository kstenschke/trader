<?php

namespace models;

require_once PATH_VENDOR . '/mahadazad/page-scraper/src/Client.php';
require_once PATH_VENDOR . '/mahadazad/page-scraper/src/PageUtility.php';
require_once PATH_VENDOR . '/mahadazad/page-scraper/src/Page/Page.php';

use models\db\Symbol;
use PageScraper\Client;

class ScraperIexCloud
{
    /**
     * Pulls stock price, name etc.
     *
     * @param  string $symbol
     * @return array|bool
     */
    public static function getCurrentData(string $symbol)
    {
        if (self::isValidSymbol($symbol)) {
            return false;
        }

        $config = Config::getConfig('scraper');
        $token = $config['token'];

        $url = self::prepareUrl($symbol, $token, $config['routes']['tops']);
        $urlOpenHighLowChange = self::prepareUrl($symbol, $token, $config['routes']['open-high-low-change']);

        $data = \file_get_contents($url);
        $dataOpenLowHighChange = \file_get_contents($urlOpenHighLowChange);

        // @todo log

        if ($data === false || '' === $data) {
            return false;
        }

        $data = \json_decode($data, true);
        $dataOpenLowHighChange = \json_decode($dataOpenLowHighChange, true);

        //echo '<pre>'; print_r($data); die();
        //echo '<pre>'; print_r($dataOlhc); die();

        // Ensure symbol was found
        return [
            'symbol' => $data['symbol'],
            'price'  => (float)$data['bidprize'],  // @todo when bidPrize / bidSize are 0: disallow buiying
            'change' => $data[''],
            'open'   => (float)$data['open']['price'],
            'close'  => (float)$data['close']['price'],
            'high'   => (float)$data['high'],
            'low'    => (float)$data['low'],
            'name'   => \trim($data[8])
        ];
    }

    public static function getHistoricData(string $symbol)
    {
        //        $config = Config::getConfig('scraper');
        //        $client = new Client([
        //            'url'         => str_replace('SYMBOL', strtoupper($symbol), $config['historic']['url']),
        //            'data_config' => [
        //                'titles' => $config['historic']['titlesXpath'],
        //                'values' => $config['historic']['valuesXpath'],
        //            ]
        //        ]);
        //
        //        return $client->fetchPage()->getData();

    }

    private static function isValidSymbol(string $symbol): bool
    {
        return \preg_match('/^\^/', $symbol) ||
               \preg_match('/,/', $symbol);
    }

    /**
     * @param $json
     * @return null|string|string[]
     */
    private static function cleanJson(string $json)
    {
        return \preg_replace(
            "#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t](//).*)#",
            '',
            $json) ?? '';
    }

    private static function prepareUrl(string $symbol, string $token, $url)
    {
        return \str_replace(['SYMBOL', 'TOKEN'], [$symbol, $token], $url);
    }
}

<?php

namespace models\db;

use models\Scraper;

class Symbol {

    /**
     * @return int
     */
    public static function getAmountSymbols(): int
    {
        $row = mysqli_fetch_array(Database::query('SELECT COUNT(*) FROM symbol'));

        return (int)$row[0];
    }

    /**
     * @param  string $name
     * @return int
     * @todo   add option (default: true) if symbol does not exist yet: store to DB
     */
    public static function getIdSymbolByName($name): int
    {
        $res = Database::query("SELECT id FROM symbol WHERE symbol = '$name'");
        return $res ? (int)mysqli_fetch_array($res)['id'] : 0;
    }

    public static function search($search)
    {
        $query = 'SELECT * FROM symbol'
               . ' JOIN exchange on symbol.id_exchange = exchange.id'
               . ' WHERE ';

        $words = explode(' ', $search);

        if (count($words) === 0) {
            $query .= '1';
        } else {
            $index = 0;
            foreach($words as $word) {
                $query .= $index > 0 ? ' AND ' : '';
                $query .= " (symbol LIKE '%$word%' OR security_name LIKE '%$word%') ";
                $index++;
            }
        }

        return Database::query($query);
    }

    /**
     * @param  int          $idSymbol
     * @return string|bool
     */
    public static function getSymbolById($idSymbol)
    {
        $idSymbol = (int)$idSymbol;
        $res = Database::query("SELECT symbol FROM symbol WHERE id = $idSymbol");

        return $res ? mysqli_fetch_array($res)['symbol'] : false;
    }

    /**
     * @param  int    $idSymbol
     * @param  string $price
     * @param  string $priceOpen
     * @param  string $priceHigh
     * @param  string $priceLow
     * @param  int    $time
     * @return bool|\mysqli_result
     * @todo   ensure rows are not inserted multiple times (UPSERT)
     */
    public static function insertPrice($idSymbol, $price, $priceOpen = null, $priceHigh = null, $priceLow = null, $time = null)
    {
        $idSymbol = (int)$idSymbol;

        if (null === $time) {
            $time = time();
        }

        if (null === $priceOpen || null === $priceHigh || null === $priceLow) {
            /** @noinspection SqlNoDataSourceInspection */
            return Database::query(
                "INSERT INTO `price` (`id_symbol`, `price`, `time`) 
                          VALUES ('$idSymbol','$price', $time)");
        }
        /** @noinspection SqlNoDataSourceInspection */
        return Database::query(
            "INSERT INTO `price` (`id_symbol`, `price`, `price_open`, `price_high`, `price_low`, `time`) 
                          VALUES ('$idSymbol','$price','$priceOpen','$priceHigh','$priceLow', $time)");
    }

    /**
     * @param int   $idSymbol
     * @param array $data
     * @todo  ensure rows are not inserted multiple times (UPSERT)
     */
    public static function saveHistoricData($idSymbol, array $data)
    {

    }

    /**
     * @return bool|\mysqli_result
     */
    public static function selectRowsForQuote()
    {
        return Database::query(
            ' SELECT  
                     price_latest.id_price, price_latest.price price_latest, price_open, price_high, price_low, price_change, price_latest.time, '
          . '        symbol.id id_symbol, symbol.symbol, symbol.security_name, '
          . '        portfolio.price price_paid '
          . ' FROM price_latest '

          . ' JOIN symbol    ON price_latest.id_symbol = symbol.id '

          . ' JOIN portfolio ON price_latest.id_symbol = portfolio.id_symbol '

          . ' GROUP BY price_latest.id_symbol'

          . ' LIMIT 0,250 '
        );
    }

    /**
     * @param  int          $idSymbol
     * @param  string|null  $symbol
     * @return bool|\mysqli_result|null
     */
    public static function fetchAndUpdatePriceDataBySymbol($idSymbol, $symbol = null)
    {
        $idSymbol = (int)$idSymbol;
        if (0 === $idSymbol) {
            return null;
        }
        if (null === $symbol) {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $symbol = self::getSymbolById($idSymbol);
            if ($symbol === false) {
                return null;
            }
        }

        $symbolData = Scraper::getCurrentData($symbol);
        if (!$symbolData || !\is_array($symbolData)) {
            return null;
        }

        $change = $symbolData['change'][0] === '+'
            ? \substr($symbolData['change'], 1)
            : $symbolData['change'];

        return self::storePrice($idSymbol, $symbolData, $change);
    }

    /**
     * @param  int    $idSymbol
     * @param  array  $symbolData
     * @param  string $change
     * @param  int    $time
     * @return bool|\mysqli_result
     */
    public static function storePrice($idSymbol, $symbolData, $change, $time = null)
    {
        if (null === $time) {
            $time = time();
        }

        return Database::query(
            'INSERT INTO price'
            . ' (id_symbol, price, price_open, price_high, price_low, price_change, time) '
            . " VALUES ('{$idSymbol}', "
            .          "'{$symbolData['price']}', "
            .          "'{$symbolData['open']}', "
            .          "'{$symbolData['high']}', "
            .          "'{$symbolData['low']}' , "
            .          "'$change', "
            .          "$time)");
    }

    /**
     * @param  int      $idSymbol
     * @param  string   $price
     * @param  string   $time
     * @return bool|\mysqli_result
     */
    public static function savePrice($idSymbol, $price, $time = null)
    {
        if (null === $time) {
            $time = time();
        }
        return Database::query(
            'INSERT INTO price (id_symbol, price, time)'
          . 'VALUES (\'' . $idSymbol . '\', \'' . $price . '\', ' . $time . ') '
        );
    }
}

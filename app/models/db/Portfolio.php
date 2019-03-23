<?php

namespace models\db;

class Portfolio {

    /**
     * @param  int          $idSymbol
     * @param  int|null     $idUser
     * @return bool|\mysqli_result
     */
    public static function selectAmountOwnedBySymbol(
        int $idSymbol,
        int $idUser = null)
    {
        if ($idUser === null) {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $idUser = User::getCurrentUserId();
        }

        return $idUser === false
            ? false
            : Database::query(
                  'SELECT number_of_share '
                . ' FROM portfolio '
                . " WHERE id_user = '$idUser' AND id_symbol = '$idSymbol'");
    }

    /**
     * @param  int|null  $idUser
     * @param  bool      $symbolNameAsKey Default: symbol ID as key
     * @return array
     * @todo implement $symbolNameAsKey option: return associative
     */
    public static function getAmountOwnedPerSymbol(
        int $idUser = null,
        bool $symbolNameAsKey = false
    ): array
    {
        $shares = [];
        $result = self::selectRows(true, $idUser);
        while ($row = \mysqli_fetch_array($result)) {
            if (!\array_key_exists($row['id_symbol'], $shares)) {
                $shares[$row['id_symbol']] = [
                    'symbol'    => $row['symbol'],
                    'price'     => $row['price'],
                    'amount'    => 0
                ];
            }
            $shares[$row['id_symbol']]['amount'] += (int)$row['number_of_share'];
        }

        return $shares;
    }

    /**
     * @param  int      $idSymbol
     * @param  int|null $idUser
     * @return bool|\mysqli_result
     */
    public static function selectRowsBySymbol($idSymbol, $idUser = null)
    {
        if (null === $idUser) {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $idUser = User::getCurrentUserId();
        }

        return $idUser === false
            ? false
            : Database::query(
                'SELECT * FROM portfolio '
              . " WHERE id_user = '$idUser' AND id_symbol = '$idSymbol'");
    }

    /**
     * @param  bool     $joinSymbols
     * @param  int|null $idUser
     * @return bool|\mysqli_result
     */
    public static function selectRows($joinSymbols = true, $idUser = null)
    {
        if (null === $idUser) {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $idUser = User::getCurrentUserId();
        }
        if ($idUser === false) {
            return false;
        }

        $query = $joinSymbols
            ?   'SELECT portfolio.*, '
              . '       symbol.symbol, symbol.security_name, '
              . '       price.price price_latest'
              . ' FROM portfolio '
              . ' JOIN symbol ON portfolio.id_symbol = symbol.id '
              . ' JOIN price  ON portfolio.id_symbol = price.id_symbol '
              . " WHERE id_user = '$idUser' "

              . ' GROUP BY portfolio.time'

              . ' ORDER BY symbol.symbol ASC '

            : "SELECT * FROM portfolio WHERE id_user = '$idUser'";

        return Database::query($query);
    }

    /**
     * @param  int       $idSymbol
     * @param  int       $qty
     * @param  string    $price
     * @param  int|null  $idUser
     * @return bool|\mysqli_result
     */
    public static function savePurchase($idSymbol, $qty, $price, $idUser = null)
    {
        if (null === $idUser) {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $idUser = User::getCurrentUserId();
        }

        return $idUser === false
            ? false
            : Database::query(
                'INSERT INTO portfolio (id_user, id_symbol, number_of_share, price, time)'
                . " VALUES ('$idUser', '$idSymbol', '$qty', '$price', '" . time() . "')");
    }

    /**
     * @param  int $idSymbol
     * @param  int $idUser
     * @return int
     */
    public static function getAmountOwned($idSymbol, $idUser): int
    {
        $result = mysqli_fetch_assoc(self::selectRowsBySymbol($idSymbol, $idUser));

        return null === $result ? 0 : $result['number_of_share'];
    }

    public static function saveWatch($idSymbol, $price, $idUser)
    {
        if (null === $idUser) {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $idUser = User::getCurrentUserId();
        }

        return $idUser === false
            ? false
            : Database::query(
                'INSERT INTO portfolio (id_user, id_symbol, number_of_share, price, time)'
                . " VALUES ('$idUser', '$idSymbol', '0', '$price', '" . time() . "')");
    }

    /**
     * @param  bool     $soldAll    DELETE from portfolio?
     * @param  int      $idSymbol
     * @param  int      $qty
     * @param  int|null $idUser
     * @return bool|\mysqli_result
     */
    public static function reduceShares($soldAll, $idSymbol, $qty, $idUser = null)
    {
        if (null === $idUser) {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $idUser = User::getCurrentUserId();
        }
        if ($idUser === false) {
            return false;
        }

        return $soldAll
            ? self::removeSymbolFromPortfolio($idSymbol, $idUser)
            : Database::query(
                "UPDATE portfolio SET number_of_share = number_of_share - '$qty'"
              . " WHERE id_user = '$idUser' AND id_symbol = '$idSymbol'");
    }

    public static function saveUnwatch($idSymbol, $idUser) {
        return self::reduceShares(true, $idSymbol, 0, $idUser);
    }

    private static function removeSymbolFromPortfolio($idSymbol, $idUser)
    {
        return Database::query(
            "DELETE FROM portfolio WHERE id_user = '$idUser' AND id_symbol = '$idSymbol'");
    }

    /**
     * @param  string   $transaction
     * @param  int      $idSymbol
     * @param  int      $qty
     * @param  string   $price
     * @param  int|null $idUser
     * @return bool|\mysqli_result
     */
    public static function archiveTransaction($transaction, $idSymbol, $qty, $price, $idUser = null)
    {
        if (null === $idUser) {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $idUser = User::getCurrentUserId();
        }

        return $idUser === false
            ? false
            : Database::query(
                'INSERT INTO archive (id_user, id_symbol, price, number_of_share, time, transaction)'
                . " VALUES ('$idUser', '$idSymbol', '$price', '$qty', '" . time() . "', '$transaction')");
    }

    /**
     * @param  int|null $idUser
     * @return bool|\mysqli_result
     */
    public static function selectAllFromArchive($idUser = null)
    {
        if (null === $idUser) {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $idUser = User::getCurrentUserId();
        }

        return $idUser === false
            ? false
            : Database::query(
                ' SELECT archive.*, '
                . '      symbol.symbol, symbol.security_name'
                . ' FROM archive '
                . ' JOIN symbol ON archive.id_symbol = symbol.id '
                . " WHERE id_user = '$idUser'"
                . ' GROUP BY time');
    }
}

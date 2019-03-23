<?php

namespace models\db;

use helpers\Http;

class User {

    /**
     * @param  string   $displayName
     * @param  string   $username
     * @param  string   $password
     * @return bool|\mysqli_result
     */
    public static function createAccount($displayName, $username, $password)
    {
        return Database::query(
            'INSERT INTO person (displayname, username, password, cash)'
          . "VALUES ('$displayName','$username','$password',10000)");
    }

    /**
     * @param  string $username
     * @return int
     */
    public static function getUserIdByName($username): int
    {
        $res = Database::query(
            "SELECT id FROM person 
             WHERE username='" . Database::escape_string($username) . "'"
        );

        return $res ? mysqli_fetch_array($res)['id'] : 0;
    }

    /**
     * @return int|bool
     */
    public static function getCurrentUserId()
    {
        return isset($_SESSION['username'])
            ? self::getUserIdByName($_SESSION['username'])
            : false;
    }

    /**
     * @param  int|null $idUser
     * @return int|bool
     */
    public static function validateUserId($idUser = null)
    {
        if (is_int($idUser)) {
            return $idUser;
        }

        $idUser = (int)$idUser;
        if (0 === $idUser) {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $idUser = self::getCurrentUserId();
        }

        return $idUser > 0 ? $idUser : false;
    }

    /**
     * @param  string $username
     * @param  string $password     password hash
     * @return bool|\mysqli_result
     */
    public static function selectAllByUsernameAndPassword($username, $password)
    {
        return Database::query(
            "SELECT * 
             FROM person 
             WHERE username='$username' 
               AND password='$password'"
        );
    }

    /**
     * @param  int|null $idUser
     * @return bool|\mysqli_result
     */
    public static function selectRowsByUser($idUser = null)
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $idUser = self::validateUserId($idUser);
        return $idUser === false
            ? false
            : Database::query("SELECT username FROM person WHERE id = '$idUser'");
    }

    /**
     * @param  int|null $idUser
     * @return string
     */
    public static function getCash($idUser = null): string
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $idUser = self::validateUserId($idUser);
        if ($idUser === false) {
            return false;
        }

        $res = Database::query("SELECT cash FROM person WHERE id = '$idUser'");
        return $res ? mysqli_fetch_array($res)['cash'] : 0;
    }

    /**
     * @param  int|null $idUser
     * @return string
     */
    public static function getLastSearch($idUser = null): string
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $idUser = self::validateUserId($idUser);
        if ($idUser === false) {
            return '';
        }

        $res = Database::query("SELECT last_search FROM person WHERE id = '$idUser'");
        return $res ? mysqli_fetch_array($res)['last_search'] : '';
    }

    /**
     * @param  string   $cashEarned
     * @param  int|null $idUser
     * @return bool|\mysqli_result
     */
    public static function addCash($cashEarned, $idUser = null)
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $idUser = self::validateUserId($idUser);
        return $idUser === false
            ? false
            : Database::query("UPDATE person SET cash = cash + '$cashEarned' WHERE id = '$idUser'");
    }

    public static function saveSearch($sword, $idUser = null)
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $idUser = self::validateUserId($idUser);
        return $idUser === false
            ? false
            : Database::query("UPDATE person SET last_search = '$sword' WHERE id = '$idUser'");
    }

    /**
     * @param  int|string   $amount
     * @param  int|null     $idUser
     * @return bool|\mysqli_result
     */
    public static function updateCash($amount, $idUser = null)
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $idUser = self::validateUserId($idUser);
        return $idUser === false
            ? false
            : Database::query("UPDATE person SET cash = '$amount' WHERE id = '$idUser'");
    }

    /**
     * @param int $idUser
     */
    public static function cheatOnCash($idUser = null): void
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $idUser = self::validateUserId($idUser);
        if ($idUser === false) {
            return;
        }
        
        if ($_GET['cheat'] === 'gimmecash') {
            $cashNew  = $_GET[ 'cash' ] ?? '10000';
            Database::query("UPDATE person SET cash = '$cashNew' WHERE id = '$idUser'");
        } elseif ($_GET['cheat'] === 'imgod') {
            $cashNew  = '999999999';
            Database::query("UPDATE person SET cash = '$cashNew' WHERE id = '$idUser'");
        }

        Http::redirect('account.phtml');
    }

    /**
     * @param  int|null $idUser
     * @return array|bool
     */
    public static function updateAllUsersAppearentCash($idUser = null)
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $idUser = self::validateUserId($idUser);
        if ($idUser === false) {
            return false;
        }
        
        $result = Database::query('SELECT * FROM person');
        while ($row = mysqli_fetch_array($result)) {
            $actual_cash = $row['cash'];
            $cash = 0;
            $portfolios = Database::query("SELECT * FROM portfolio WHERE id = '$idUser'");
            while ($portfolio = mysqli_fetch_array($portfolios)) {
                $symbol = $portfolio['symbol'];
                $prices = Database::query("SELECT price FROM price WHERE symbol = '$symbol'");
                $price = mysqli_fetch_array($prices);

                $cash += $price['price'] * $portfolio['number_of_share'];
            }
            $cash += $actual_cash;
            Database::query("UPDATE person SET apparent_cash = '$cash' WHERE id ='$idUser'");
        }

        return [$result, $row];
    }

    /**
     * @param  string   $new
     * @param  int|null $idUser
     * @return bool|\mysqli_result
     */
    public static function updatePassword($new, $idUser = null)
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $idUser = self::validateUserId($idUser);
        return $idUser === false
            ? false
            : Database::query("UPDATE person SET password = '$new' WHERE id = '$idUser'");
    }

    /**
     * @param  string   $compare
     * @param  int|null $idUser 
     * @return bool
     */
    public static function isCurrentPasswordHash($compare, $idUser = null)
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $idUser = self::validateUserId($idUser);
        return $idUser === false
            ? false
            : mysqli_fetch_array(
                Database::query("SELECT password FROM person WHERE id = '$idUser'")
              )['password'] === $compare;
    }
}

<?php

namespace models\db;

class Preference {

    // Preference identifiers
    const PREF_IDENTIFIER_THEME        = 'theme';
    const PREF_IDENTIFIER_LANGUAGE     = 'language';
    const PREF_IDENTIFIER_FORMAT_MONEY = 'format_money';

    /**
     * @param  string     $preference
     * @param  string|int $value
     * @param  int|null   $idArea
     * @param  bool|null  $hasMultiple Update if pre-existing, or allow multiple?
     * @param  int|null   $idUser
     * @return bool
     */
    public static function savePreference(
        string $preference,
        $value,
        int $idArea = 0,
        bool $hasMultiple = false,
        int $idUser = null
    ): bool
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $idUser = User::validateUserId($idUser);
        if ($idUser === false) {
            return false;
        }

        if (!$hasMultiple && self::hasPreference($preference, $idUser, $idArea)) {
            self::updatePreference($preference, $value, $idUser, $idArea);
        } else {
            self::insertPreference($preference, $value, $idUser , $idArea);
        }

        return true;
    }

    /**
     * @param  string $preference
     * @param  string $value
     * @param  int|null   $idUser
     * @param  int    $idArea
     * @return bool|\mysqli_result
     */
    public static function insertPreference(
        string $preference,
        string $value,
        int $idUser = null,
        int $idArea = 0
    )
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $idUser = User::validateUserId($idUser);

        return $idUser === false
            ? false
            : Database::query(
                'INSERT INTO preference (id_user, preference, id_area, value) '
                . 'VALUES (\'' . $idUser . '\', 
                           \'' . $preference . '\', 
                           \'' . $idArea . '\', 
                           \'' . $value . '\') ');
    }

    /**
     * @param  string $preference
     * @param  string $value
     * @param  int    $idUser
     * @param  int    $idArea
     * @return bool|\mysqli_result
     */
    public static function updatePreference(
        string $preference,
        string $value,
        int $idUser = null,
        int $idArea = 0)
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $idUser = User::validateUserId($idUser);

        return $idUser === false
            ? false
            :  Database::query(
                "UPDATE preference 
                 SET value = '$value'
                 WHERE id_user    = '$idUser' 
                   AND preference = '$preference'
                   AND id_area    = '$idArea' ");
    }

    public static function hasPreference(
        string $preference,
        int $idUser = null,
        int $idArea = 0
    )
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $idUser = User::validateUserId($idUser);

        $row = self::getPreference($preference, $idArea, $idUser, true);
        return false !== $row && [] !== $row;
    }

    /**
     * @param  string   $preference
     * @param  int|null $idArea
     * @param  int|null $idUser
     * @param  bool     $returnRow
     * @return bool|string
     */
    public static function getPreference(
        string $preference,
        int $idArea = 0,
        int $idUser = null,
        bool $returnRow = false
    )
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $idUser = User::validateUserId($idUser);
        if ($idUser === false) {
            return false;
        }

        $result = Database::query(
            "SELECT * FROM preference 
             WHERE id_user    = '$idUser' 
               AND preference = '$preference' 
               AND id_area    = $idArea "
        );
        $row = \mysqli_fetch_array($result);

        return $returnRow ? $row : $row['value'];
    }
}

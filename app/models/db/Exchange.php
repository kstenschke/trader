<?php

namespace models\db;

class Exchange {

    /**
     * @return bool|\mysqli_result
     */
    public static function selectAll()
    {
        return Database::query('SELECT * FROM exchange WHERE 1 ORDER BY exchange');
    }
}

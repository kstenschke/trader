<?php

/** @noinspection UntrustedInclusionInspection */
require_once '../bootstrap.php';

$data  = file_get_contents('data/symbols/nasdaqtraded.txt');
$lines = explode("\n", $data);

$amountImported = 0;
foreach ($lines as $line) {
    if (0 === strpos($line, 'Y|')) {
        $columns = explode('|', $line);
        $symbol = $columns[1];
        $name   = mysqli_escape_string($connection, $columns[2]);
        $idExchange = 1;

        models\db\Database::query(
              ' INSERT INTO `symbol` (`symbol`, `security_name`, `id_exchange`) '
            . " VALUES ('$symbol','$name', '$idExchange'); "
        );

        $amountImported++;
    }
}

die($amountImported . ' symbols imported.');
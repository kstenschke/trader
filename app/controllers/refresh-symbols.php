<?php

/** @noinspection UntrustedInclusionInspection */
/** @noinspection PhpIncludeInspection */
require_once 'app/bootstrap.php';

if (isset($_GET['ids'])) {
    $ids = \explode(',', $_GET['ids']);
    foreach($ids as $id) {
        models\db\Symbol::fetchAndUpdatePriceDataBySymbol($id);
    }
    \header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
    \header('Location: ' . $_SERVER['HTTP_REFERER']);
}

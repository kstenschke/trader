<?php

/** @noinspection UntrustedInclusionInspection */
/** @noinspection PhpIncludeInspection */
require_once 'app/bootstrap.php';

if (isset($_GET)) {
    // Fetch recent data for given symbol
    models\db\Symbol::fetchAndUpdatePriceDataBySymbol($_GET['id']);
}

\header('location: quotes.phtml');

<?php

/** @noinspection UntrustedInclusionInspection */
/** @noinspection PhpIncludeInspection */
require_once 'app/bootstrap.php';
models\Auth::restrictAccess();

// Lookup the symbol provided by the user, and display it's information
if (!isset($_POST['symbol']) || empty($_POST['symbol'])) {
    \trigger_error('You must provide a symbol.', E_USER_ERROR);
}

$symbol = models\Scraper::getCurrentData($_POST['symbol']);
/** @var array $symbol */
if (!is_array($symbol) /*|| (int)$symbol['price'] === 0*/) {
    \trigger_error(
        'This does not appear to be a valid stock symbol.',
        E_USER_ERROR);
}

// Store symbol data in table->price
$idSymbol = models\db\Symbol::getIdSymbolByName($symbol['symbol']);
if ($idSymbol > 0) {
    models\db\Symbol::insertPrice(
        $idSymbol, $symbol['price'], $symbol['open'], $symbol['high'], $symbol['low']);
//    $data = models\Scraper::getHistoricData($_POST[ 'symbol' ]);
//    models\db\Symbol::saveHistoricData($idSymbol, $data);
}

require PATH_APP . '/views/quote-details.phtml';

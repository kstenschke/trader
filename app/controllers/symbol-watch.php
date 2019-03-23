<?php

/** @noinspection UntrustedInclusionInspection */
/** @noinspection PhpIncludeInspection */
require_once 'app/bootstrap.php';
models\Auth::restrictAccess();

// Error checking if any field was left empty
if (!isset($_GET['symbol']) || empty($_GET['symbol'])) {
    \trigger_error('Provide a symbol in its respective field', E_USER_ERROR);
}

$idSymbol = models\db\Symbol::getIdSymbolByName($_GET['symbol']);
if ($idSymbol === false) {
    \trigger_error('Symbol not found.', E_USER_ERROR);
}

$symbol   = models\db\Database::escape_string($_GET['symbol']);
$stock    = models\Scraper::getCurrentData($symbol);
$idUser   = models\db\User::getCurrentUserId();

$amountOwned = models\db\Portfolio::getAmountOwned($idSymbol, $idUser);
if ($amountOwned > 0) {
    \trigger_error(
        'Already in portfolio, cancelled adding to watchlist.',
        E_USER_ERROR);
}

// Insert symbol into the portfolio
$stockSymbol = \strtoupper($stock['symbol']);
$result      = models\db\Portfolio::saveWatch($idSymbol, $stock['price'], $idUser);
if (!$result) {
    \trigger_error('Updating portfolio failed', E_USER_ERROR);
}

// Update current value of stock in price table
models\db\Symbol::savePrice($idSymbol, $stock['price']);

helpers\Http::redirect('portfolio.phtml');

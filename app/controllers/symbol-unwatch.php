<?php

/** @noinspection UntrustedInclusionInspection */
/** @noinspection PhpIncludeInspection */
require_once 'app/bootstrap.php';
models\Auth::restrictAccess();

// Error checking if any field was left empty
if (!isset($_GET['symbol']) || empty($_GET['symbol'])) {
    \trigger_error('Provide a symbol in its respective field', E_USER_ERROR);
}

$idSymbol = \is_numeric($_GET['symbol'])
    ? (int)$_GET['symbol']
    : models\db\Symbol::getIdSymbolByName($_GET['symbol']);
if ($idSymbol === 0) {
    \trigger_error('Symbol not found or invalid.', E_USER_ERROR);
}

$symbol = models\db\Database::escape_string($_GET['symbol']);
$stock  = models\Scraper::getCurrentData($symbol);
$idUser = models\db\User::getCurrentUserId();

$amountOwned = models\db\Portfolio::getAmountOwned($idSymbol, $idUser);
if ($amountOwned > 0) {
    \trigger_error(
        'Stock is in portfolio, cancelled removing from watchlist.',
        E_USER_ERROR);
}

// Delete symbol from portfolio
$result = models\db\Portfolio::saveUnwatch($idSymbol, $idUser);
if (!$result) {
    \trigger_error('Updating portfolio failed', E_USER_ERROR);
}

helpers\Http::redirect('portfolio.phtml');

<?php

/** @noinspection UntrustedInclusionInspection */
/** @noinspection PhpIncludeInspection */
require_once 'app/bootstrap.php';
models\Auth::restrictAccess();

// Error checking if any field was left empty
if (!isset($_GET['symbol']) ||
    empty($_GET['symbol'])
) {
    \trigger_error('Provide a symbol in its respective field', E_USER_ERROR);
}

if (!isset($_GET['qty']) ||
    empty($_GET['qty'])
) {
    \trigger_error('Provide number of stocks you want to buy in its respective field', E_USER_ERROR);
}

$idSymbol = models\db\Symbol::getIdSymbolByName($_GET['symbol']);
if ($idSymbol === false) {
    \trigger_error('Symbol not found.', E_USER_ERROR);
}
if ((int)$_GET['qty'] < 0) {
    \trigger_error('Number of stock less than zero', E_USER_ERROR);
}

// Okay, lets buy
$symbol   = models\db\Database::escape_string($_GET['symbol']);
$qty      = (int)$_GET['qty'];
$idUser   = models\db\User::getCurrentUserId();

// Check if enough cash to make this purchase
$cash = models\db\User::getCash();
if ($cash < 0 || !$cash) {
    \trigger_error(
        "You're either broke or I can't figure out how much money do you have!",
        E_USER_ERROR);
}

$stock = models\Scraper::getCurrentData($symbol);
if (!$stock || (int)$stock['price'] === 0) {
    \trigger_error(
        'Look-up current price for symbol failed',
        E_USER_ERROR);
}

$cost = $stock['price'] * $qty;
if ($cash < $cost) {
    \trigger_error(
        "You don't have enough money to buy {$qty} shares of {$stock['symbol']}'s stock",
        E_USER_ERROR);
}

// Update cash
$difference = (float) ($cash - $cost);
if (!models\db\User::updateCash($difference)) {
    \trigger_error('Update cash balance failed.', E_USER_ERROR);
}

// Insert symbol into the portfolio
$stockSymbol = \strtoupper($stock['symbol']);
$result = models\db\Portfolio::savePurchase($idSymbol, $qty, $stock['price'], $idUser);
if (!$result) {
    \trigger_error('Updating portfolio failed', E_USER_ERROR);
}

$result = models\db\Portfolio::archiveTransaction('BUY', $idSymbol, $qty, $stock['price'], $idUser);
if (!$result) {
    \trigger_error('Store transaction to history failed', E_USER_ERROR);
}

// Update current value of stock in price table
models\db\Symbol::savePrice($idSymbol, $stock['price']);

helpers\Http::redirect('portfolio.phtml');

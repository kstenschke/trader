<?php

namespace models;

require_once PATH_VENDOR . '/gerardojbaez/money/src/Currency.php';
require_once PATH_VENDOR . '/gerardojbaez/money/src/helpers.php';
require_once PATH_VENDOR . '/gerardojbaez/money/src/Money.php';

use /** @noinspection PhpUndefinedNamespaceInspection */
    Gerardojbaez\Money\Currency;
use /** @noinspection PhpUndefinedNamespaceInspection */
    Gerardojbaez\Money\Money;

/**
 * Monetary: financial entities (money, currency, etc.) handling
 */
class Monetary {

    /** @var string */
    private static $activeMoneyFormat;

    public static function getShownCurrencies() : array
    {
        /** @noinspection PhpUndefinedClassInspection */
        $allCurrencies      = Currency::getAllCurrencies();
        $shownCurrencyCodes = Config::getConfig('currencies');

        $shownCurrencies = [];
        foreach($shownCurrencyCodes as $currencyCode) {
            $shownCurrencies[] = $allCurrencies[$currencyCode];
        }

        return $shownCurrencies;
    }

    /**
     * @param  string $currency
     * @return string
     */
    public static function formatMoneyByCurrencyCode($currency) : string
    {
        /** @noinspection PhpUndefinedClassInspection */
        return (new Money(1000000, $currency['code']))->format();
    }

    /**
     * @return bool|string
     * @singleton
     */
    public static function getActiveMoneyFormatName()
    {
        if (null === self::$activeMoneyFormat) {
            self::$activeMoneyFormat = db\Preference::hasPreference(db\Preference::PREF_IDENTIFIER_FORMAT_MONEY)
                ? db\Preference::getPreference(db\Preference::PREF_IDENTIFIER_FORMAT_MONEY)
                : 'default';
        }

        return self::$activeMoneyFormat;
    }
}

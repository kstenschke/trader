<?php

namespace models;

use helpers\Strings;

class Area {

    // Area IDs used e.g. for identification in preferences
    const AREA_ID_UNDEFINED = 0;
    const AREA_ID_PORTFOLIO = 1;
    const AREA_ID_QUOTES    = 2;
    const AREA_ID_BUY       = 3;
    const AREA_ID_SELL      = 4;

    /** @var string */
    private static $activePageName;

    /**
     * @return string
     * @singleton
     */
    public static function getActivePageName(): string
    {
        if (null === self::$activePageName) {
            $pageName = \str_replace(
                '.' . FILE_ENDING_PHP,
                '',
                \substr($_SERVER[ 'REQUEST_URI' ], 1));
            if (Strings::endsNumeric($pageName)) {
                $pageName = Strings::removeTrailingNumbers($pageName);
            }
            self::$activePageName = $pageName;
        }

        return self::$activePageName;
    }

    public static function getIdAreaByIdentifier(string $areaIdentifier): bool
    {
        $constant = 'Area::AREA_ID_' . \strtoupper($areaIdentifier);

        return \defined($constant) ? \constant($constant) : self::AREA_ID_UNDEFINED;
    }
}

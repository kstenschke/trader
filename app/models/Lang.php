<?php

namespace models;

use helpers\Files;
use helpers\Strings;

require_once PATH_VENDOR . '/philipp15b/php-i18n/i18n.class.php';

class Lang {

    /** @var string e.g. 'en' / 'de' */
    private static $activeLanguage;

    /**
     * @param string $forcedLang
     * @throws \Exception
     */
    public static function init($forcedLang = null)
    {
        if (null === $forcedLang) {
            $forcedLang = self::getActiveLanguage();
        }

        $i18n = new \i18n();

        $i18n->setCachePath(PATH_CACHE);
        $i18n->setFilePath(PATH_DEPLOY_LANG . '/lang_{LANGUAGE}.json');
        $i18n->setFallbackLang('en');
        $i18n->setPrefix('I');

        // Force english, even if another user language is available
        $i18n->setForcedLang($forcedLang);
        $i18n->setSectionSeperator('_');

        $i18n->init();

        // Load active language's translation
        $languageFiles = Files::scanDir(PATH_CACHE, FILE_ENDING_PHP, false, 'php_i18n_');
        foreach($languageFiles as $languageFile) {
            if (Strings::endsWith('_de.cache.php', $languageFile)) {
                /** @noinspection PhpIncludeInspection */
                require_once $languageFile;
            }
        }
    }

    /**
     * @return array    Available language's keys, sorted alphabetical, e.g. ['en', 'de',...]
     */
    public static function getLanguageKeys(): array
    {
        $languageFiles = Files::scanDir(PATH_DEPLOY_LANG, FILE_ENDING_JSON, false, '', true);
        $languages = [];
        foreach ($languageFiles as $languageFile) {
            $key = \substr(\explode('_', $languageFile)[ 1 ], 0, -5);
            $languages[$key] = i('Language' . \ucfirst($key));
        }

        \asort($languages);

        return \array_keys($languages);
    }

    public static function getActiveLanguage($toLower = false)
    {
        if (null === self::$activeLanguage) {
            self::$activeLanguage = db\Preference::hasPreference(db\Preference::PREF_IDENTIFIER_LANGUAGE)
                ? db\Preference::getPreference(db\Preference::PREF_IDENTIFIER_LANGUAGE)
                : Config::getConfig('defaultLanguage');
        }

        return $toLower ? \strtolower(self::$activeLanguage) : self::$activeLanguage;
    }
}

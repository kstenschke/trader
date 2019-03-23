<?php

namespace models;

use helpers\Files;
use helpers\Strings;

class JavaScript {

    /**
     * Return script inclusion-tag
     * If not yet there: Merge + minify JavaScripts (to update: delete cached file js.min.js)
     *
     * @return string
     */
    public static function getScriptTag(): string
    {
//        $pathJsMinified     = PATH_ASSETS . '/js-' . Lang::getActiveLanguage() . '.min.js';
        $pathJsMinified     = PATH_ASSETS . '/js.min.js';
        $forceRebuildAssets = Config::getConfig('bootstrap')['force-rebuild-js'] === '1';
        if ($forceRebuildAssets || !\file_exists($pathJsMinified)) {
            self::mergeAndMinifyAndTranslateJs($pathJsMinified);
        }

        return '<script type="text/javascript" src="var/assets/js.min.js"></script>';
    }

    /**
     * If 'js.min.js' not in assets directory yet: merge and minify JavaScripts
     *
     * @param string $pathJsMinified
     */
    private static function mergeAndMinifyAndTranslateJs($pathJsMinified)
    {
        $appScripts    = Files::scanDir(PATH_DEPLOY_ASSETS . '/js', 'js', false, 'Trader.');
        \sort($appScripts);

        $vendorScripts = Config::getConfig('vendor-libraries');

        $scripts = \array_merge($vendorScripts, $appScripts);
        $js      = '';
        foreach ($scripts as $scriptFile) {
            if (!Strings::startsWith($scriptFile, PATH_APP)) {
                $scriptFile = PATH_APP . DIRECTORY_SEPARATOR . $scriptFile;
            }
            //if (!self::isMinifiedJsFilename($scriptFile)) {
                $js .= \file_get_contents($scriptFile) . "\n";
            //}
            //echo $scriptFile . ' - ' . strlen($js) . "\n";
        }
        $js = self::translateJsI18nMarkers($js);

        $minifyJs = \getenv('environment') !== ENVIRONMENT_DEVELOPMENT
            || Config::getConfig('bootstrap')['minify-js'] === '1';

        Files::write($pathJsMinified, $minifyJs ? \JSMinPlus::minify($js) : $js);
    }

    private static function isMinifiedJsFilename($scriptFile)
    {
        return !\strpos($scriptFile, '.min.js') === false;
    }

    /**
     * Find and translate i18n strings in given JavaScript
     *
     * @param  string $js
     * @return string
     */
    private static function translateJsI18nMarkers($js): string
    {
        \preg_match_all('/###I::([a-zA-Z]+)###/', $js, $matches);
        foreach ($matches[1] as $word) {
            $js = \str_replace("###I::$word###", i($word), $js);
        }

        return $js;
    }
}

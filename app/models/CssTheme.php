<?php

namespace models;

use helpers\Files;

require_once PATH_VENDOR . '/nitra/php-min/CssMin/CssMin.php';

class CssTheme {

    /** @var string */
    private static $activeThemeName;

    /**
     * @return string   Link tag referencing the active stylesheet
     */
    public static function getStylesheetLink(): string
    {
        $pathDefaultCss     = PATH_ASSETS . '/default.css';
        $forceRebuildAssets = Config::getConfig('bootstrap')['force-rebuild-css'] === '1';
        if ($forceRebuildAssets) {
            if (\file_exists($pathDefaultCss)) {
                \chmod($pathDefaultCss, 777);
                \unlink($pathDefaultCss);
            }
            self::removeCompiledCssFiles();
        }

        if ($forceRebuildAssets ||
            !\file_exists($pathDefaultCss)
        ) {
            $scssFilePaths = Files::scanDir(PATH_DEPLOY_ASSETS . '/scss', 'scss');
            (new self())
                ->compileScss($scssFilePaths, $pathDefaultCss)
                ->minifyDefaultAndGenerateAlternativeStylesheets();
        }

        $activeThemeName = self::getActiveThemeName();

        return '<link rel="stylesheet" type="text/css" href="var/assets/' . $activeThemeName .'.min.css"/>';
    }

    /**
     * @param  bool $toLower
     * @return string
     * @singleton
     */
    public static function getActiveThemeName(bool $toLower = false)
    {
        if (null === self::$activeThemeName) {
            if (db\Preference::hasPreference(db\Preference::PREF_IDENTIFIER_THEME)) {
                self::$activeThemeName = db\Preference::getPreference(db\Preference::PREF_IDENTIFIER_THEME);
            }
            if (empty(self::$activeThemeName)) {
                self::$activeThemeName = 'default';
            }
        }

        return $toLower ? \strtolower(self::$activeThemeName) : self::$activeThemeName;
    }

    /**
     * Compiles default.scss to CSS
     *
     * @param array|string $pathScss    One or multiple SCSS file(s), multiple are concatenated
     * @param string $pathCss
     * @return CssTheme
     * @fluent
     */
    private function compileScss(string $pathScss, string $pathCss)
    {
        if (\is_array($pathScss)) {
            $sCss = '';
            /** @noinspection ForeachSourceInspection */
            foreach($pathScss as $pathItem) {
                $sCss .= \file_get_contents($pathItem) . "\n";
            }
        } else {
            $sCss = \file_get_contents($pathScss);
        }
        require_once PATH_VENDOR . '/leafo/scssphp/scss.inc.php';

        $css = (new \scssc())->compile('$color: #abc;' . "\n" . $sCss . "\n");

        Files::write($pathCss, $css);

        return $this;
    }

    /**
     * Generate dark version, minify stylesheet
     *
     * @return CssTheme
     * @fluent
     */
    public function minifyDefaultAndGenerateAlternativeStylesheets(): CssTheme
    {
        $themes = Config::getConfig('themes');
        $stylesheets = [];
        foreach($themes as $theme) {
            $stylesheets[$theme['name']] = \str_replace(
                'PATH_ASSETS',
                PATH_ASSETS,
                $theme['path']);
        }

        $indexTheme = 0;
        foreach ($stylesheets as $pathStylesheet) {
            $pathStylesheetMin = \str_replace('.css', '.min.css', $pathStylesheet);
            if (!\file_exists($pathStylesheetMin)) {
                $css = \file_get_contents($stylesheets['default']);
                if ($indexTheme > 0) {
                    $css = self::convertColorsToTheme($css, $indexTheme);
                }

                Files::write($pathStylesheetMin, \CssMin::minify($css));
            }
            $indexTheme++;
        }

        return $this;
    }

    private static function convertColorsToTheme(string $css, int $indexTheme): string
    {
        $alternativeThemeName = Config::getConfig()['themes'][$indexTheme]['name'];

        /** @var string $themeClassName
         *  @see DarkTheme
         */
        $themeClassName   = \ucfirst($alternativeThemeName) . 'Theme';

        /** @noinspection PhpIncludeInspection */
        require_once PATH_APP . '/deploy/themes/' . $themeClassName . '.' . FILE_ENDING_PHP;

        $complimentaryColors = $themeClassName::getComplimentaryColors();

        \preg_match_all('/#([a-f0-9]{6}|[a-f0-9]{3})/i', $css, $matches);
        $defaultColors = \array_unique($matches[1]);
        $replaceColors = [];
        foreach ($defaultColors as $key => $color) {
            $color = \strtolower($color);
            $replaceColors[$key] = !\array_key_exists($color, $complimentaryColors)
                ? $color
                : $complimentaryColors[$color];
        }

        return \str_replace($defaultColors, $replaceColors, $css);
    }

    private static function removeCompiledCssFiles()
    {
        $themes = Config::getConfig('themes');
        foreach ($themes as $theme) {
            $pathTheme = \str_replace('PATH_ASSETS', PATH_ASSETS, $theme['path']);
            if (\file_exists($pathTheme)) {
                \chmod($pathTheme, 777);
                \unlink($pathTheme);
            }
        }
    }
}

<?php

// Display/suppress errors
if (\getenv('environment') === ENVIRONMENT_DEVELOPMENT) {
    \ini_set('display_errors', '1');
    \ini_set('display_startup_errors', '1');
    \ini_set('error_reporting', E_ALL | E_ERROR | E_PARSE);
} else {
    \error_reporting(0);
}

if (\session_status() === PHP_SESSION_NONE) {
    \session_start();
}

// Constants
const FILE_ENDING_PHP  = 'php';
const FILE_ENDING_PHTML= 'phtml';
const FILE_ENDING_JSON = 'json';

// Root, app, vendor PHP files
\define('PATH_ROOT', \dirname(__DIR__ ));
\define('PATH_APP',    PATH_ROOT . '/app');
\define('PATH_VENDOR', PATH_ROOT . '/vendor');

// Files to be deployed
\define('PATH_DEPLOY_ASSETS', PATH_APP . '/deploy/assets');
\define('PATH_DEPLOY_LANG', PATH_APP . '/deploy/lang');

// Deployed files
\define('PATH_CACHE',    PATH_ROOT . '/var/cache');
\define('PATH_ASSETS', PATH_ROOT . '/var/assets');

// Setup PHP class autoloader
\spl_autoload_register(function ($className) {
    $classPath = \str_replace('\\', DIRECTORY_SEPARATOR, $className);
    $classType = \explode('\\', $className)[0];
    if (   $classType === 'models' || $classType === 'helpers') {
        $classPath = \str_replace("$classType/models/", "$classType/", $classPath);
        /** @noinspection PhpIncludeInspection */
        $pathFile = PATH_APP . DIRECTORY_SEPARATOR . $classPath . '.' . FILE_ENDING_PHP;
        if (\file_exists($pathFile)) {
            require_once $pathFile;
            return;
        }
    }

    $path = PATH_APP . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR;
    /** @noinspection PhpIncludeInspection */
    $pathFile = (\substr($className, -9) === 'Exception')
        ? $path . 'exceptions' . DIRECTORY_SEPARATOR . $className
        : $path . $className . '.' . FILE_ENDING_PHP;

    if (\file_exists($pathFile)) {
        require_once $pathFile;
    }
});

// Init composer autoloader for vendor libraries
require_once __DIR__ . '/../vendor/autoload.php';

// Setup error handler
require_once PATH_APP . '/errorHandler.php';
\set_error_handler('errorHandler');

// Init i18n
require_once PATH_VENDOR . '/philipp15b/php-i18n/i18n.class.php';
$activeLanguage = models\Lang::getActiveLanguage();
models\Lang::init($activeLanguage);

// Load active language's translation
$languageFiles = helpers\Files::scanDir(PATH_CACHE, FILE_ENDING_PHP, false, 'php_i18n_');
foreach($languageFiles as $languageFile) {
    if (helpers\Strings::endsWith("_$activeLanguage.cache.php", $languageFile)) {
        /** @noinspection PhpIncludeInspection */
        require_once $languageFile;
    }
}
function i($string, $args = null) {
    return \vsprintf(\constant('I::' . $string), $args);
}

// Setup database connection
$dbConfig   = models\Config::getConfig('database');
$connection = models\db\Database::getDbConnection($dbConfig);
if (!$connection) {
    \trigger_error(
        'Could not connect to database.<br/>Please check your configuration.',
        E_USER_ERROR);
}
if (models\Config::getConfig('bootstrap')['check-DB-exists'] === '1' &&
    !\mysqli_select_db($connection, $dbConfig['database'])
) {
    \trigger_error('Database does not exist.', E_USER_ERROR);
}
unset($connection);

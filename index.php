<?php

// Environment constants
const ENVIRONMENT_DEVELOPMENT = 'dev';
const ENVIRONMENT_PRODUCTION  = 'prod';

\putenv('environment=' . ENVIRONMENT_DEVELOPMENT);

/** @noinspection UntrustedInclusionInspection */
/** @noinspection PhpIncludeInspection */
require_once 'app/bootstrap.php';

// Redirect to login page if not logged-in
$path = \ltrim($_SERVER['REQUEST_URI'], '/');
/** @noinspection SuspiciousBinaryOperationInspection */
if (empty($path) ||
    (
        $path !== 'login.phtml' &&
        $path !== 'login2.phtml' &&
        $path !== 'register.phtml' &&
        $path !== 'register2.phtml'
    )
    && (
        !isset($_SESSION['loggedin']) ||
        \trim($_SESSION['loggedin']) === '')
) {
    \header('location: login.phtml');
    exit();
}

// Rewrite URLs: homepage is login-form or portfolio, load pages from app/views
$ending = \strpos($path, FILE_ENDING_PHP) === strlen($path) - 3
    ? FILE_ENDING_PHP
    : FILE_ENDING_PHTML;
$pageName = \explode('.' . $ending, $path)[0];

$pageName = helpers\Strings::startsWith($pageName, 'ajax-')
    // URLs w/ "ajax-" prefix are rewritten to app/controllers/<pageName>.php
    ? helpers\Strings::replaceFirst($pageName, 'ajax-', '/controllers/')
    // URLs w/o "ajax-" prefix are page views, in app/views/<pageName>.php
    : '/views/' . $pageName;

$pathViewController = PATH_APP . $pageName . '.' . $ending;
if (!\file_exists($pathViewController)) {
    // Fallback to PHTML
    $pathViewController = false !== \strpos($pathViewController, '.php')
        ? PATH_APP . \str_replace(FILE_ENDING_PHP, FILE_ENDING_PHTML, $pageName) . '.' . FILE_ENDING_PHTML
        : PATH_APP . $pageName . FILE_ENDING_PHTML;
}
if (
    $path === 'index.php' ||
    empty(\trim($path)) ||
    !\file_exists($pathViewController)
) {
    \trigger_error('File not found: ' . $pathViewController, E_USER_ERROR);
}

// Render page or result to AJAX request
/** @noinspection PhpIncludeInspection */
include $pathViewController;

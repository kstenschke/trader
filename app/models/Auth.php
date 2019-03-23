<?php

namespace models;

class Auth
{
    /**
     * Ensure only logged-in users have access
     */
    public static function  restrictAccess()
    {
        $path = \ltrim($_SERVER['REQUEST_URI'], '/');
        if ($path !== 'login.phtml' && empty($_SESSION['loggedin'])) {
            \header('location: login.phtml');
            exit();
        }
    }

    /**
     * Destroy session and session cookie
     */
    public static function logout(): void
    {
        $_SESSION = [];
        $sessionName = \session_name();

        if (!empty($_COOKIE[$sessionName])) {
            \setcookie($sessionName, '', time() - 42000);
        }

        \session_destroy();
    }
}

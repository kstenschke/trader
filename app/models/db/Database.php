<?php

namespace models\db;

use models\Config;

require_once PATH_APP . '/models/Config.php';

class Database {

    /** @var Database */
    private static $instance;

    /** @var \mysqli */
    private $dbConnection;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    public function __clone() {
        throw new \CannotCloneSingletonException('Cannot clone singleton: Database');
    }

    /**
     * @return Database
     * @singleton
     */
    private static function getInstance()
    {
        if (self::$instance === null) {
            $className = __CLASS__;
            self::$instance = new $className;
        }

        return self::$instance;
    }

    /**
     * @param  array $config
     * @return Database
     */
    private static function initConnection($config = null)
    {
        $db = self::getInstance();

        if (null === $config) {
            $config = Config::getConfig('database');
        }

        $db->dbConnection = new \mysqli(
            $config['host'],
            $config['user'],
            $config['password'],
            $config['database']);

        $db->dbConnection->set_charset('utf8');

        return $db;
    }

    /**
     * @param  array $config
     * @return \mysqli|null
     */
    public static function getDbConnection($config = null)
    {
        try {
            return self::initConnection($config)->dbConnection;
        } catch (\Exception $ex) {
            echo 'Failed opening database connection. ' . $ex->getMessage();
        }

        return null;
    }

    /**
     * Encodes post data
     *
     * @param  string $string
     * @return string
     */
    public static function escape_string(string $string): string
    {
        return \mysqli_real_escape_string(self::getDbConnection(), $string);
    }

    /**
     * @param  string $query
     * @return bool|\mysqli_result
     */
    public static function query(string $query)
    {
        $connection = self::getDbConnection();
        $result     = \mysqli_query($connection, $query);

        $error = mysqli_error($connection);
        if (!empty($error)) {
            die('SQL error: ' . $error . '<br/>'
              . 'Query: ' . $query . '<br/>'
              . 'Backtrace:<pre>' . \print_r(\debug_backtrace(), true) . '</pre>'
            );
        }

        return $result;
    }

    /**
     * @return int
     */
    public static function affected_rows(): int
    {
        return \mysqli_affected_rows(self::getDbConnection());
    }
}

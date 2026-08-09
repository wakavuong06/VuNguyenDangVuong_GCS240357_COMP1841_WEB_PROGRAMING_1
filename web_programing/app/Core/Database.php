<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    // The real values come from config/config.php, so this class
    // never needs editing when the project moves to another machine.
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    // One shared connection per request: without this, every Model
    // object would open its own connection to MySQL.
    private static $sharedConnection = null;

    public function __construct()
    {
        $this->host     = DB_HOST;
        $this->db_name  = DB_NAME;
        $this->username = DB_USER;
        $this->password = DB_PASS;
    }

    public function connect()
    {
        if (self::$sharedConnection !== null) {
            return self::$sharedConnection;
        }

        $this->conn = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            // Throw exceptions on SQL errors and return associative arrays.
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            die("Database connection failed: " . $exception->getMessage()
                . "<br>Check DB_NAME / DB_USER / DB_PASS in config/config.php "
                . "and make sure database/web_programing.sql has been imported.");
        }

        self::$sharedConnection = $this->conn;
        return $this->conn;
    }
}

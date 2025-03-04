<?php
class Database {
    private static $instance = null;
    private $conn;

    //private $host = "HoyoWorld.serv.gs";
    private $host = "localhost";
    private $port = "3306"; // Change this if your MySQL server runs on a different port
    private $dbname = "field_service_db"; // Change this to your database name
    private $username = "root"; // Change this if needed
    //private $password = "aaa12345"; // Change this if needed
    private $password = "";

    private function __construct() {
        try {
            $dsn = "mysql:host=$this->host;port=$this->port;dbname=$this->dbname;charset=utf8";
            $this->conn = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            die("Database Connection Failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->getConnection();
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>

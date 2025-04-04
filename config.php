<?php
class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $this->connection = new mysqli("localhost", "root", "", "location_voiture");
        if ($this->connection->connect_error) {
            die("Échec de la connexion MySQL: " . $this->connection->connect_error);
        }
        $this->connection->set_charset("utf8mb4");
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    // Empêcher le clonage
    private function __clone() { }

    // Correction ici : la méthode __wakeup() doit être publique
    public function __wakeup() { }
}
function db() {
    return Database::getInstance()->getConnection();
}

?>

<?php
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        $this->connection = new mysqli(
            "localhost",  // serveur
            "root",       // utilisateur
            "",           // mot de passe
            "location_voiture" // base de données
        );
        
        if ($this->connection->connect_error) {
            die("Échec de la connexion MySQL: " . $this->connection->connect_error);
        }
        
        // Définir l'encodage
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
    
    // Empêcher la désérialisation
    private function __wakeup() { }
}

// Fonction utilitaire pour accéder facilement à la connexion
function db() {
    return Database::getInstance()->getConnection();
}
?>
<?php
class Database {
    private static $instance = null;
    private $connection;

    // Constructeur privé pour empêcher l'instanciation directe
    private function __construct() {
        $this->connection = new mysqli("localhost", "root", "", "location_voiture");

        // Vérification de la connexion
        if ($this->connection->connect_error) {
            die("Échec de la connexion MySQL: " . $this->connection->connect_error);
        }

        $this->connection->set_charset("utf8mb4");
    }

    // Méthode pour obtenir l'instance unique de la base de données
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Retourne la connexion MySQL
    public function getConnection() {
        return $this->connection;
    }

    // Empêcher le clonage de l'objet
    private function __clone() { }

    // Empêcher la désérialisation
    public function __wakeup() {
        throw new Exception("Désérialisation non autorisée.");
    }
}

// Fonction utilitaire pour récupérer la connexion
function db() {
    return Database::getInstance()->getConnection();
}
?>

<?php
class Database {
    private static $instance = null;
    private $connection;

    // Constructeur privé pour empêcher l'instanciation directe
    private function __construct() {
        try {
            $this->connection = new PDO("mysql:host=localhost;dbname=location_voiture;charset=utf8mb4", "root", "");
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    // Méthode pour obtenir l'instance unique de la base de données
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Retourne la connexion PDO
    public function getConnection() {
        return $this->connection;
    }

    // Empêcher le clonage de l'objet
    private function __clone() {}

    // Empêcher la désérialisation
    public function __wakeup() {
        throw new Exception("Désérialisation non autorisée.");
    }
}

// Fonction utilitaire pour récupérer la connexion PDO
function db() {
    return Database::getInstance()->getConnection();
}
?>

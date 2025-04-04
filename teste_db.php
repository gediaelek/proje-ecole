<?php
require_once 'config.php';
try {
    $conn = db();
    echo "Connexion réussie !";
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}

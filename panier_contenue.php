<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Vérifier si le panier existe
if (!isset($_SESSION['panier']) || empty($_SESSION['panier'])) {
    echo json_encode([]);
    exit;
}

$ids = implode(',', array_fill(0, count($_SESSION['panier']), '?'));

try {
    $pdo = db();
    $stmt = $pdo->prepare("SELECT id, marque, modele, prix_jour, image FROM vehicules WHERE id IN ($ids)");
    $stmt->execute($_SESSION['panier']);
    $voitures = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($voitures);
} catch (PDOException $e) {
    error_log("Erreur panier_contenu: " . $e->getMessage());
    echo json_encode([]);
}
?>

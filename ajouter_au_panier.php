<?php
session_start();
require_once 'config.php';

if (!isset($_POST['vehicule_id'])) {
    echo json_encode(['error' => 'ID manquant']);
    exit;
}

$id = intval($_POST['vehicule_id']);
$conn = db();

// Récupérer les infos du véhicule
$stmt = $conn->prepare("SELECT * FROM vehicules WHERE id = ?");
$stmt->execute([$id]);
$vehicule = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vehicule) {
    echo json_encode(['error' => 'Véhicule introuvable']);
    exit;
}

// Ajouter au panier (empêche doublon)
$_SESSION['panier'][$id] = $vehicule;

// Retourner le nombre d’éléments
echo json_encode(['count' => count($_SESSION['panier'])]);

<?php
session_start();

if (!isset($_SESSION["utilisateur_id"])) {
    die("Vous devez être connecté pour ajouter un véhicule au panier.");
}

// Vérifier si l'ID du véhicule est envoyé
if (isset($_POST['vehicule_id'])) {
    $vehicule_id = $_POST['vehicule_id'];

    // Vérifier si le panier existe en session
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    // Ajouter l'ID du véhicule au panier (éviter les doublons)
    if (!in_array($vehicule_id, $_SESSION['panier'])) {
        $_SESSION['panier'][] = $vehicule_id;
    }

    // Rediriger vers la même page (catalogue.php)
    header("Location: catalogue.php");
    exit();
} else {
    echo "Erreur : Aucune voiture sélectionnée.";
}
?>

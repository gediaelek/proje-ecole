<?php
session_start();

if (isset($_POST['vehicule_id'])) {
    $vehicule_id = $_POST['vehicule_id'];

    if (isset($_SESSION['panier'])) {
        $_SESSION['panier'] = array_diff($_SESSION['panier'], [$vehicule_id]);
    }
}

// Retour au panier
header("Location: panier.php");
exit();
?>

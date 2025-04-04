<?php
session_start();
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

if (isset($_POST['vehicule_id'])) {
    $id = $_POST['vehicule_id'];
    if (!in_array($id, $_SESSION['panier'])) {
        $_SESSION['panier'][] = $id;
    }
}
?>

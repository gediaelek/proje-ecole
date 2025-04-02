<?php
session_start();

// Données d'authentification
$admin_user = "admin";
$admin_password = "password123"; // Change ce mot de passe

if ($_POST['username'] === $admin_user && $_POST['password'] === $admin_password) {
    $_SESSION['is_admin'] = true;
    header("Location: upload.php");
    exit;
} else {
    echo "Nom d'utilisateur ou mot de passe incorrect.";
}
?>

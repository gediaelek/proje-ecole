<?php
session_start();
require_once 'db.php'; // Assurez-vous d'avoir ce fichier pour la connexion à la DB

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // 1. Vérification que les champs ne sont pas vides
    if (empty($username) || empty($password)) {
        die("Tous les champs sont obligatoires");
    }

    // 2. Recherche de l'utilisateur dans la base
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // 3. Vérification du mot de passe
        if ($user && password_verify($password, $user['password'])) {
            // Connexion réussie
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = ($user['username'] === 'admin'); // Optionnel pour les admins
            
            header("Location: upload.php");
            exit;
        } else {
            echo "Nom d'utilisateur ou mot de passe incorrect.";
        }
    } catch (PDOException $e) {
        die("Erreur de base de données: " . $e->getMessage());
    }
}
?>
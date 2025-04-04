<?php
session_start();
require_once 'connection';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Tous les champs sont obligatoires.";
        header("Location: login.php");
        exit;
    }

    // Protection contre le brute force
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }

    if ($_SESSION['login_attempts'] >= 3) {
        $_SESSION['error'] = "Trop de tentatives. Réessayez plus tard.";
        header("Location: login.php");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        $hashedPassword = $user['password'] ?? '';

        if ($user && password_verify($password, $hashedPassword)) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = ($user['username'] === 'admin');

            $_SESSION['login_attempts'] = 0; // Réinitialiser le compteur
            header("Location: upload.php");
            exit;
        } else {
            $_SESSION['login_attempts']++;
            $_SESSION['error'] = "Nom d'utilisateur ou mot de passe incorrect.";
            header("Location: login.php");
            exit;
        }
    } catch (PDOException $e) {
        error_log("Erreur de base de données: " . $e->getMessage());
        $_SESSION['error'] = "Une erreur est survenue, veuillez réessayer plus tard.";
        header("Location: login.php");
        exit;
    }
}
?>

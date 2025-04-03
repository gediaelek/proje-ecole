<?php
session_start();
error_reporting(E_ALL); // Activer l'affichage des erreurs
ini_set('display_errors', 1);

// Connexion DB
$conn = new mysqli("localhost", "root", "", "location_voiture");
if ($conn->connect_error) {
    die("Erreur DB: " . $conn->connect_error);
}

// Nettoyer les inputs
$email = trim($_POST['email'] ?? '');
$mot_de_passe = $_POST['mot_de_passe'] ?? '';

if (empty($email) || empty($mot_de_passe)) {
    die("Email et mot de passe requis");
}

// Requête préparée
$sql = "SELECT id, nom, prenom, mot_de_passe FROM utilisateurs WHERE email = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erreur préparation: " . $conn->error);
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Vérification du mot de passe
    if (password_verify($mot_de_passe, $user['mot_de_passe'])) {
        // Regénérer l'ID de session pour plus de sécurité
        session_regenerate_id();
        
        // Stocker les informations utilisateur dans la session
        $_SESSION['id'] = $user['id'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['prenom'] = $user['prenom'];

        // Rediriger vers la page d'accueil ou tableau de bord
        header("Location: index.html");
        exit;
    } else {
        die("Mot de passe incorrect. <a href='login.html'>Réessayer</a>");
    }
} else {
    die("Email non trouvé. <a href='login.html'>Réessayer</a>");
}
?>

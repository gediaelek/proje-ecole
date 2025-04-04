<?php
session_start();
require_once 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

// Vérifier si le panier existe et n'est pas vide
if (!isset($_SESSION['panier']) || empty($_SESSION['panier'])) {
    echo "Votre panier est vide.";
    exit;
}

$user_id = $_SESSION['user_id'];
$pdo = db();
$success = [];
$errors = [];

foreach ($_SESSION['panier'] as $car_id => $details) {
    $start_date = $details['start_date'];
    $end_date = $details['end_date'];

    // Vérifier si la voiture est disponible
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE car_id = ? AND status = 'confirmée' AND (start_date < ? AND end_date > ?)");
    $stmt->execute([$car_id, $end_date, $start_date]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $errors[] = "La voiture ID $car_id est déjà réservée entre $start_date et $end_date.";
        continue;
    }

    // Insérer la réservation
    $stmt = $pdo->prepare("INSERT INTO reservations (user_id, car_id, start_date, end_date, status) VALUES (?, ?, ?, ?, 'en attente')");
    if ($stmt->execute([$user_id, $car_id, $start_date, $end_date])) {
        $success[] = "Réservation pour la voiture ID $car_id ajoutée avec succès.";
    } else {
        $errors[] = "Erreur lors de la réservation de la voiture ID $car_id.";
    }
}

// Vider le panier si au moins une réservation a été faite
if (!empty($success)) {
    unset($_SESSION['panier']);
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Validation du panier</title>
    <link rel="stylesheet" href="style.css"> <!-- ton fichier de style si existant -->
</head>
<body>
    <h2>Validation du panier</h2>
    
    <?php foreach ($success as $msg): ?>
        <p style="color: green;"><?php echo htmlspecialchars($msg); ?></p>
    <?php endforeach; ?>

    <?php foreach ($errors as $msg): ?>
        <p style="color: red;"><?php echo htmlspecialchars($msg); ?></p>
    <?php endforeach; ?>

    <a href="catalogue.php">Retour au catalogue</a>
</body>
</html>

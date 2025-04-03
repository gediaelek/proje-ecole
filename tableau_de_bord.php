<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    header("Location: index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord</title>
</head>
<body>
    <h2>Bienvenue, <?php echo $_SESSION['prenom'] . " " . $_SESSION['nom']; ?> !</h2>
    <p>Vous êtes connecté.</p>
    <a href="logout.php">Se déconnecter</a>
</body>
</html>

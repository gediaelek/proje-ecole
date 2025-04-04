<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Tableau de bord</title>
</head>
<body>
  <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['user_email']); ?> !</h1>
  <p>Vous êtes connecté avec succès.</p>
  <a href="logout.php">Se déconnecter</a>
</body>
</html>

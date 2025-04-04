<?php
require_once "config.php";
$message = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $conn = db();
    $sql = "SELECT * FROM utilisateurs WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($mot_de_passe, $user['mot_de_passe'])) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                header("Location: index.html");
                exit();
            } else {
                $message = "Mot de passe incorrect.";
            }
        } else {
            $message = "Aucun utilisateur trouvé avec cet email.";
        }
        $stmt->close();
    } else {
        $message = "Erreur lors de la préparation de la requête.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <form action="login.php" method="POST">
        <h1>Se connecter</h1>

        <?php if (!empty($message)) : ?>
            <p style="color: red; text-align: center;"><?php echo $message; ?></p>
        <?php endif; ?>

        <div class="inputs">
            <input type="email" name="email" placeholder="Email" required />
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required />
        </div>

        <div align="center">
            <button type="submit">Se connecter</button>
        </div>
    </form>
</body>
</html>

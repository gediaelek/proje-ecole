<?php
// Connexion à la base de données
$servername = "localhost"; // Modifier si nécessaire
$username = "root";        // Modifier si nécessaire
$password = "";            // Modifier si nécessaire
$dbname = "location_voiture"; // Nom de ta base de données

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Récupérer les données du formulaire
$nom = htmlspecialchars($_POST['nom']);
$prenom = htmlspecialchars($_POST['prenom']);
$email = htmlspecialchars($_POST['email']);
$telephone = htmlspecialchars($_POST['telephone']);
$mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT); // Hashage du mot de passe

// Vérifier si l'email existe déjà
$sql_check = "SELECT * FROM utilisateurs WHERE email = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    echo "Cet email est déjà utilisé. <a href='inscription.html'>Réessayer</a>";
    exit();
}

// Insérer les données dans la base de données
$sql = "INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $nom, $prenom, $email, $telephone, $mot_de_passe);

if ($stmt->execute()) {
    echo "Inscription réussie ! <a href='login.html'>Connectez-vous</a>";
} else {
    echo "Erreur lors de l'inscription : " . $conn->error;
}

// Fermer la connexion
$stmt->close();
$conn->close();
?>

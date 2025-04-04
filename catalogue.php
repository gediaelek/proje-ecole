<?php
require_once 'config.php';
session_start();

$conn = db();

// Vérification de la connexion
if (!$conn) {
    die("Erreur de connexion à la base de données");
}

// Exécuter la requête SQL
$sql = "SELECT id, marque, modele, categorie, prix_jour, description, image, disponible FROM vehicules";
$result = $conn->query($sql);

// Vérification des erreurs SQL
if (!$result) {
    die("Erreur SQL : " . $conn->error);
}

// Afficher les résultats
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div>";
        echo "<h2>" . $row["marque"] . " " . $row["modele"] . "</h2>";
        echo "<p>Catégorie : " . $row["categorie"] . "</p>";
        echo "<p>Prix par jour : " . $row["prix_jour"] . "€</p>";
        echo "<p>" . $row["description"] . "</p>";
        if (!empty($row["image"])) {
            echo "<img src='images/" . $row["image"] . "' alt='Image du véhicule' width='200'>";
        }
        if ($row["disponible"]) {
            echo "<p style='color:green;'>Disponible</p>";
            echo "<form action='ajouter_au_panier.php' method='POST'>";

            echo "<input type='hidden' name='vehicule_id' value='" . $row["id"] . "'>";
            echo "<button type='submit'>Ajouter au panier</button>";
            echo "</form>";
        } else {
            echo "<p style='color:red;'>Non disponible</p>";
        }
        echo "</div><hr>";
    }
} else {
    echo "Aucun véhicule trouvé.";
}
?>

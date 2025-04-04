<?php
session_start();
require_once 'config.php';

$conn = db();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["utilisateur_id"])) {
    die("Vous devez être connecté pour voir votre panier.");
}

// Vérifier si le panier existe et contient des véhicules
if (!isset($_SESSION['panier']) || empty($_SESSION['panier'])) {
    echo "Votre panier est vide.";
    exit();
}

// Récupérer les véhicules ajoutés au panier
$ids = implode(',', array_map('intval', $_SESSION['panier']));
$sql = "SELECT id, marque, modele, prix_jour, image FROM vehicules WHERE id IN ($ids)";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div>";
        echo "<h2>" . $row["marque"] . " " . $row["modele"] . "</h2>";
        echo "<p>Prix par jour : " . $row["prix_jour"] . "€</p>";
        if (!empty($row["image"])) {
            echo "<img src='images/" . $row["image"] . "' width='150'>";
        }
        echo "<form action='supprimer_du_panier.php' method='POST'>";
        echo "<input type='hidden' name='vehicule_id' value='" . $row["id"] . "'>";
        echo "<button type='submit'>Supprimer</button>";
        echo "</form>";
        echo "</div><hr>";
    }
} else {
    echo "Aucune voiture trouvée.";
}
?>

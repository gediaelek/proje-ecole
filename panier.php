<?php


session_start();
var_dump($_SESSION['panier'] ?? []);
exit;


echo "<pre>";
print_r($_SESSION['panier']);
echo "</pre>";

// Vérifier si le panier existe
if (!isset($_SESSION['panier']) || empty($_SESSION['panier'])) {
    echo "<h2>Votre panier est vide.</h2>";
    exit();
}

$ids = implode(',', $_SESSION['panier']);
$sql = "SELECT * FROM vehicules WHERE id IN ($ids)";
$result = $conn->query($sql);

$total = 0;

if ($result && $result->num_rows > 0) {
    echo "<h2>Votre panier</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Image</th><th>Marque</th><th>Modèle</th><th>Prix/jour</th><th>Action</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td><img src='images/" . $row['image'] . "' width='100'></td>";
        echo "<td>" . $row['marque'] . "</td>";
        echo "<td>" . $row['modele'] . "</td>";
        echo "<td>" . $row['prix_jour'] . "€</td>";
        echo "<td><a href='supprimer_du_panier.php?id=" . $row['id'] . "'>Supprimer</a></td>";
        echo "</tr>";
        
        $total += $row['prix_jour'];
    }
    
    echo "</table>";
    echo "<h3>Total : " . $total . "€</h3>";
    echo "<a href='valider_panier.php'>Valider la réservation</a>";
} else {
    echo "<h2>Votre panier est vide.</h2>";
}
?>

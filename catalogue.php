<?php
require_once 'config.php';
session_start();

// Initialiser le panier s'il n'existe pas
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

$conn = db();

// Récupération des véhicules avec PDO
$sql = "SELECT id, marque, modele, categorie, prix_jour, description, image, disponible FROM vehicules";
$stmt = $conn->prepare($sql);
$stmt->execute();
$vehicules = $stmt->fetchAll(PDO::FETCH_ASSOC);
$nombre_de_voitures = count($vehicules);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catalogue de voitures</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .voiture { border: 1px solid #ccc; padding: 10px; margin-bottom: 15px; border-radius: 8px; }
        img { max-width: 200px; border-radius: 6px; }
        #panierIcon { position: fixed; top: 20px; right: 20px; cursor: pointer; font-size: 24px; }
        #popupPanier {
            display: none;
            position: fixed;
            right: 20px;
            top: 60px;
            width: 350px;
            background: white;
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 8px;
            z-index: 999;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }
        .popup-item { margin-bottom: 10px; display: flex; align-items: center; }
        .popup-item img { max-width: 60px; height: auto; margin-right: 10px; border-radius: 4px; }
        .valider-btn {
            background-color: #28a745; color: white; border: none; padding: 10px;
            border-radius: 6px; cursor: pointer; width: 100%;
        }
    </style>
</head>
<body>

<h1>Catalogue de voitures</h1>

<!-- Icône du panier -->
<div id="panierIcon">🛒 (<span id="count"><?php echo count($_SESSION['panier']); ?></span>)</div>

<!-- Popup du panier -->
<div id="popupPanier">
    <h3>Mon panier</h3>
    <div id="contenuPanier">
        <!-- Contenu du panier AJAX ici -->
    </div>
    <p>Total : <span id="totalPanier">0 €</span></p>
    <button class="valider-btn" onclick="demanderPaiement()">Valider</button>
</div>

<?php
// Affichage des véhicules
if ($nombre_de_voitures > 0) {
    foreach ($vehicules as $row) {
        echo "<div class='voiture'>";
        echo "<h2>" . htmlspecialchars($row["marque"]) . " " . htmlspecialchars($row["modele"]) . "</h2>";
        echo "<p>Catégorie : " . htmlspecialchars($row["categorie"]) . "</p>";
        echo "<p>Prix par jour : " . htmlspecialchars($row["prix_jour"]) . " €</p>";
        echo "<p>" . htmlspecialchars($row["description"]) . "</p>";

        // Affichage image avec vérification
        if (!empty($row["image"])) {
            $imagePath = "images/" . $row["image"];
            if (file_exists($imagePath)) {
                echo "<img src='" . htmlspecialchars($imagePath) . "' alt='Image véhicule'>";
            } else {
                echo "<p style='color:red;'>Image non trouvée : " . htmlspecialchars($imagePath) . "</p>";
                echo "<img src='images/default.jpg' alt='Image par défaut'>"; // image par défaut si absente
            }
        }

        // Bouton d'ajout au panier
        if ($row["disponible"]) {
            echo "<button class='ajouterPanier' data-id='" . $row["id"] . "'>Ajouter au panier</button>";
        } else {
            echo "<p style='color:red;'>Non disponible</p>";
        }
        echo "</div>";
    }
} else {
    echo "<p style='color:red;'>Aucun véhicule trouvé.</p>";
}
?>

<!-- JS AJAX -->
<script>
    const panierIcon = document.getElementById('panierIcon');
    const popup = document.getElementById('popupPanier');

    panierIcon.onclick = () => {
        popup.style.display = popup.style.display === 'block' ? 'none' : 'block';
        chargerPanier();
    };

    document.querySelectorAll('.ajouterPanier').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            fetch('ajouter_au_panier.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'vehicule_id=' + id
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('count').innerText = data.count;
                chargerPanier();
            });
        });
    });

    function chargerPanier() {
        fetch('panier_contenu.php')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('contenuPanier');
                const total = document.getElementById('totalPanier');
                container.innerHTML = '';
                let somme = 0;
                data.forEach(item => {
                    somme += parseFloat(item.prix_jour);
                    container.innerHTML += `
                        <div class="popup-item">
                            <img src="images/${item.image}" alt="" onerror="this.src='images/default.jpg'" />
                            <div>
                                ${item.marque} ${item.modele}<br>
                                ${item.prix_jour} € / jour
                            </div>
                        </div>`;
                });
                total.innerText = somme.toFixed(2) + ' €';
            });
    }

    function demanderPaiement() {
        const mode = prompt("Veuillez entrer le mode de paiement (Carte, PayPal, Espèces...)");
        if (mode) {
            alert("Merci ! Vous avez choisi : " + mode + ". Nous allons traiter votre réservation.");
            // Redirection possible ici vers valider_panier.php
        }
    }
</script>

</body>
</html>

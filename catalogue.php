<?php
require_once 'config.php';
session_start();

// Initialiser le panier s'il n'existe pas
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

$conn = db();

// Récupération des véhicules (avec PDO)
$stmt = $conn->prepare("SELECT * FROM vehicules");
$stmt->execute();
$vehicules = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catalogue de voitures</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .voiture { border: 1px solid #ccc; padding: 10px; margin-bottom: 15px; border-radius: 8px; }
        img { max-width: 200px; display: block; margin-bottom: 10px; }
        #panierIcon { position: fixed; top: 20px; right: 20px; cursor: pointer; }
        #popupPanier {
            display: none;
            position: fixed;
            right: 20px;
            top: 60px;
            width: 300px;
            background: white;
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 8px;
            z-index: 999;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }
        .popup-item { margin-bottom: 10px; }
        .popup-item img { max-width: 60px; vertical-align: middle; margin-right: 10px; }
    </style>
</head>
<body>

<h1>Catalogue de voitures</h1>

<!-- Icône de panier -->
<div id="panierIcon">🛒 (<span id="count"><?php echo count($_SESSION['panier']); ?></span>)</div>

<!-- Popup du panier -->
<div id="popupPanier">
    <h3>Mon panier</h3>
    <div id="contenuPanier"></div>
    <p>Total : <span id="totalPanier">0 €</span></p>
</div>

<?php if ($vehicules): ?>
    <?php foreach ($vehicules as $voiture): ?>
        <div class="voiture">
            <h2><?= htmlspecialchars($voiture["marque"]) ?> <?= htmlspecialchars($voiture["modele"]) ?></h2>
            <p>Catégorie : <?= htmlspecialchars($voiture["categorie"]) ?></p>
            <p>Prix par jour : <?= htmlspecialchars($voiture["prix_jour"]) ?> €</p>
            <p><?= htmlspecialchars($voiture["description"]) ?></p>
            <?php if (!empty($voiture["image"])): ?>
                <img src="images/<?= htmlspecialchars($voiture["image"]) ?>" alt="Image véhicule">
            <?php endif; ?>
            <?php if ($voiture["disponible"]): ?>
                <button class="ajouterPanier" data-id="<?= $voiture["id"] ?>">Ajouter au panier</button>
            <?php else: ?>
                <p style='color:red;'>Non disponible</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Aucun véhicule trouvé.</p>
<?php endif; ?>

<!-- JS AJAX et Panier -->
<script>
    const panierIcon = document.getElementById('panierIcon');
    const popup = document.getElementById('popupPanier');

    panierIcon.onclick = () => {
        popup.style.display = popup.style.display === 'block' ? 'none' : 'block';
        chargerPanier();
    };

    document.querySelectorAll('.ajouterPanier').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
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
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('contenuPanier');
            const total = document.getElementById('totalPanier');
            container.innerHTML = '';
            let somme = 0;
            data.forEach(item => {
                somme += parseFloat(item.prix_jour);
                container.innerHTML += `
                    <div class="popup-item">
                        <img src="images/${item.image}" alt="" />
                        ${item.marque} ${item.modele} - ${item.prix_jour} €
                    </div>`;
            });
            total.innerText = somme.toFixed(2) + ' €';
        });
    }
</script>

</body>
</html>

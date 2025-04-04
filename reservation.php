<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Vous devez être connecté pour réserver."]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Content-Type: application/json");

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(["error" => "Requête invalide."]);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $car_id = $_POST['car_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    if (empty($car_id) || empty($start_date) || empty($end_date)) {
        echo json_encode(["error" => "Tous les champs sont obligatoires."]);
        exit;
    }

    if (strtotime($start_date) < time()) {
        echo json_encode(["error" => "La date de début ne peut pas être dans le passé."]);
        exit;
    }

    if (strtotime($end_date) <= strtotime($start_date)) {
        echo json_encode(["error" => "La date de fin doit être après la date de début."]);
        exit;
    }

    try {
        // Vérifier si la voiture existe et récupérer son prix
        $stmt = $pdo->prepare("SELECT price_per_day FROM cars WHERE id = ?");
        $stmt->execute([$car_id]);
        $car = $stmt->fetch();

        if (!$car) {
            echo json_encode(["error" => "La voiture sélectionnée n'existe pas."]);
            exit;
        }

        // Vérifier si la voiture est déjà réservée
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations 
            WHERE car_id = ? AND status = 'confirmée'
            AND (start_date < ? AND end_date > ?)");
        $stmt->execute([$car_id, $end_date, $start_date]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            echo json_encode(["error" => "Cette voiture est déjà réservée à ces dates."]);
            exit;
        }

        // Insérer la réservation
        $stmt = $pdo->prepare("INSERT INTO reservations (user_id, car_id, start_date, end_date) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $car_id, $start_date, $end_date]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Regenerate token
            echo json_encode(["success" => "Réservation effectuée avec succès !"]);
        }
    } catch (PDOException $e) {
        error_log($e->getMessage(), 3, 'errors.log'); // Log the error
        echo json_encode(["error" => "Une erreur est survenue. Veuillez réessayer."]);
    }
    exit;
}

// Générer un token CSRF pour le formulaire
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<form id="reservationForm">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    
    <select name="category" id="category" required>
        <option value="">Choisir une catégorie</option>
        <?php foreach ($categories as $category) {
            echo "<option value='{$category['id']}'>{$category['name']}</option>";
        } ?>
    </select>

    <select name="brand" id="brand" required>
        <option value="">Choisir une marque</option>
    </select>

    <select name="car_id" id="car_id" required>
        <option value="">Choisir une voiture</option>
    </select>

    <input type="date" name="start_date" id="start_date" required>
    <input type="date" name="end_date" id="end_date" required>
    
    <p><strong>Prix total :</strong> <span id="total_price">0</span>€</p>

    <button type="submit">Réserver</button>
</form>

<p id="message"></p>

<script>
document.getElementById('category').addEventListener('change', function() {
    let categoryId = this.value;
    fetch('get_brands.php?category_id=' + categoryId)
        .then(response => response.json())
        .then(data => {
            let brandSelect = document.getElementById('brand');
            brandSelect.innerHTML = '<option value="">Chargement...</option>';
            if (data.length === 0) {
                brandSelect.innerHTML = '<option value="">Aucune marque disponible</option>';
            } else {
                data.forEach(brand => {
                    brandSelect.innerHTML += `<option value="${brand.id}">${brand.name}</option>`;
                });
            }
        });
    document.getElementById('brand').innerHTML = '<option value="">Sélectionner une marque</option>';
    document.getElementById('car_id').innerHTML = '<option value="">Sélectionner une voiture</option>';
});

document.getElementById('brand').addEventListener('change', function() {
    let brandId = this.value;
    fetch('get_cars.php?brand_id=' + brandId)
        .then(response => response.json())
        .then(data => {
            let carSelect = document.getElementById('car_id');
            carSelect.innerHTML = '<option value="">Sélectionner une voiture</option>';
            data.forEach(car => {
                carSelect.innerHTML += `<option value="${car.id}" data-price="${car.price_per_day}">${car.model} - ${car.price_per_day}€/jour</option>`;
            });
        });
});

function updateTotalPrice() {
    let carSelect = document.getElementById('car_id');
    let pricePerDay = carSelect.options[carSelect.selectedIndex]?.getAttribute('data-price') || 0;
    let startDate = new Date(document.getElementById('start_date').value);
    let endDate = new Date(document.getElementById('end_date').value);
    
    if (startDate && endDate && endDate > startDate) {
        let days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
        document.getElementById('total_price').textContent = (pricePerDay * days).toFixed(2);
    } else {
        document.getElementById('total_price').textContent = "0";
    }
}

document.getElementById('start_date').addEventListener('change', updateTotalPrice);
document.getElementById('end_date').addEventListener('change', updateTotalPrice);
document.getElementById('car_id').addEventListener('change', updateTotalPrice);

document.getElementById('reservationForm').addEventListener('submit', function (e) {
    const startDate = new Date(document.getElementById('start_date').value);
    const endDate = new Date(document.getElementById('end_date').value);
    const today = new Date();

    if (startDate < today) {
        e.preventDefault();
        alert("La date de début ne peut pas être dans le passé.");
        return;
    }

    if (endDate <= startDate) {
        e.preventDefault();
        alert("La date de fin doit être après la date de début.");
        return;
    }

    e.preventDefault();
    
    const formData = new FormData(this);
    fetch('reservation.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        let messageElem = document.getElementById('message');
        if (data.success) {
            messageElem.style.color = "green";
            messageElem.textContent = data.success;
            this.reset();
            document.getElementById('total_price').textContent = "0";
        } else {
            messageElem.style.color = "red";
            messageElem.textContent = data.error;
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        let messageElem = document.getElementById('message');
        messageElem.style.color = "red";
        messageElem.textContent = "Une erreur est survenue. Veuillez réessayer.";
    });
});
</script>

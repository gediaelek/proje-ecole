<?php
session_start();
require_once 'config.php'; // Inclusion de la connexion PDO

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

    $pdo = db(); // Récupération de la connexion PDO

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
        $car = $stmt->fetch(PDO::FETCH_ASSOC);

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

$categories = db()->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>

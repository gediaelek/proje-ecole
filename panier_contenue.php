<?php
session_start();

if (!isset($_SESSION['panier'])) {
    echo json_encode([]);
    exit;
}

echo json_encode(array_values($_SESSION['panier']));

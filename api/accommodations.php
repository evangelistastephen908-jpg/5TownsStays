<?php
// api/accommodations.php - Dynamic JSON API for search & filter
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$municipality = isset($_GET['municipality']) ? trim($_GET['municipality']) : '';
$type = isset($_GET['type']) ? trim($_GET['type']) : '';
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 10000;

$sql = "SELECT * FROM accommodations WHERE status = 'Active'";
$params = [];

if ($search !== '') {
    $sql .= " AND (name LIKE ? OR description LIKE ? OR address LIKE ?)";
    $st = "%{$search}%";
    $params[] = $st; $params[] = $st; $params[] = $st;
}

if ($municipality !== '') {
    $sql .= " AND municipality = ?";
    $params[] = $municipality;
}

if ($type !== '') {
    $sql .= " AND type = ?";
    $params[] = $type;
}

if ($max_price < 10000) {
    $sql .= " AND price_start <= ?";
    $params[] = $max_price;
}

$sql .= " ORDER BY featured DESC, rating DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll();

echo json_encode([
    'status' => 'success',
    'count' => count($data),
    'data' => $data
]);

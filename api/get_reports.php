<?php
header('Content-Type: application/json');
require_once 'db.php';

$user_id = $_GET['user_id'] ?? null;
if (!$user_id) {
    http_response_code(400);
    echo json_encode(['error' => 'user_id required']);
    exit;
}

$stmt = $pdo->prepare("SELECT id, filename, uploaded_at FROM reports WHERE user_id = ? ORDER BY uploaded_at DESC");
$stmt->execute([$user_id]);

$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($reports);

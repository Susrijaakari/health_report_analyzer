<?php
header('Content-Type: application/json');
require_once 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $data['user_id'] ?? null;
$height = $data['height'] ?? null;
$weight = $data['weight'] ?? null;

if (!$user_id || !$height || !$weight) {
    http_response_code(400);
    echo json_encode(['error' => 'user_id, height and weight required']);
    exit;
}

$stmt = $pdo->prepare("UPDATE users SET height = ?, weight = ? WHERE id = ?");
$stmt->execute([$height, $weight, $user_id]);

echo json_encode(['status' => 'success']);

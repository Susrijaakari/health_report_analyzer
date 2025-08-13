<?php
header('Content-Type: application/json');
require_once 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$report_id = $data['report_id'] ?? null;
$risk_level = $data['risk_level'] ?? '';
$diet_recommendations = $data['diet_recommendations'] ?? '';
$activity_recommendations = $data['activity_recommendations'] ?? '';

if (!$report_id) {
    http_response_code(400);
    echo json_encode(['error' => 'report_id required']);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO analysis (report_id, risk_level, diet_recommendations, activity_recommendations, analyzed_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->execute([$report_id, $risk_level, $diet_recommendations, $activity_recommendations]);

echo json_encode(['status' => 'success']);

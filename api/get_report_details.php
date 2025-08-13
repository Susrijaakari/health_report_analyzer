<?php
header('Content-Type: application/json');
require_once 'db.php';

$report_id = $_GET['report_id'] ?? null;
if (!$report_id) {
    http_response_code(400);
    echo json_encode(['error' => 'report_id required']);
    exit;
}

$stmt = $pdo->prepare("
    SELECT r.id, r.filename, r.uploaded_at,
           a.risk_level, a.diet_recommendations, a.activity_recommendations, a.analyzed_at
    FROM reports r
    LEFT JOIN analysis a ON r.id = a.report_id
    WHERE r.id = ?
");
$stmt->execute([$report_id]);

$data = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$data) {
    http_response_code(404);
    echo json_encode(['error' => 'Report not found']);
    exit;
}

echo json_encode($data);

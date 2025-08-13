<?php
header('Content-Type: application/json');
require_once 'db.php';

$report1 = $_GET['report1'] ?? null;
$report2 = $_GET['report2'] ?? null;

if (!$report1 || !$report2) {
    http_response_code(400);
    echo json_encode(['error' => 'Both report1 and report2 required']);
    exit;
}

$stmt = $pdo->prepare("
    SELECT r.id, r.filename, a.risk_level, a.diet_recommendations, a.activity_recommendations
    FROM reports r
    LEFT JOIN analysis a ON r.id = a.report_id
    WHERE r.id IN (?, ?)
");
$stmt->execute([$report1, $report2]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($results) < 2) {
    http_response_code(404);
    echo json_encode(['error' => 'Reports not found or incomplete']);
    exit;
}

echo json_encode($results);

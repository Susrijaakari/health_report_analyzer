<?php
header('Content-Type: application/json');
require_once 'db.php';

// Check if file and user_id are sent
if (!isset($_FILES['report']) || !isset($_POST['user_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Report file and user_id required']);
    exit;
}

$user_id = intval($_POST['user_id']);
$targetDir = __DIR__ . '/../uploads/';
if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

$file = $_FILES['report'];
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$allowed = ['pdf', 'png', 'jpg', 'jpeg'];

if (!in_array(strtolower($ext), $allowed)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file type']);
    exit;
}

$newFileName = uniqid('report_') . '.' . $ext;
$targetFile = $targetDir . $newFileName;

if (move_uploaded_file($file['tmp_name'], $targetFile)) {
    // Save file info in DB
    $stmt = $pdo->prepare("INSERT INTO reports (user_id, filename, uploaded_at) VALUES (?, ?, NOW())");
    $stmt->execute([$user_id, $newFileName]);

    echo json_encode(['status' => 'success', 'report_id' => $pdo->lastInsertId(), 'filename' => $newFileName]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to upload file']);
}

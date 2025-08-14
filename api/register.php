<?php
file_put_contents('debug.log', "Raw input: " . file_get_contents('php://input') . "\n", FILE_APPEND);
require_once 'db.php';

header("Content-Type: application/json");
// Read raw POST input
$input = file_get_contents('php://input');
$data = json_decode($input, true);
// DEBUG - SHOW what PHP received
if ($data === null) {
    echo json_encode([
        "error" => "Invalid JSON or decoding failed",
        "raw_input" => $input
    ]);
    exit;
}
// Validate required fields
if (!isset($data['name'], $data['email'], $data['password'])) {
    echo json_encode(["error" => "Name, email and password are required"]);
    exit;
}

// Optional fields
$age = $data['age'] ?? null;
$gender = $data['gender'] ?? null;
$height = $data['height'] ?? null;
$weight = $data['weight'] ?? null;

// Sanitize inputs
$name = trim($data['name']);
$email = trim($data['email']);
$password = password_hash($data['password'], PASSWORD_DEFAULT); // secure hash

// Check if user already exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->rowCount() > 0) {
    echo json_encode(["error" => "User already exists"]);
    exit;
}

// Insert into DB
$stmt = $pdo->prepare("
    INSERT INTO users (name, email, password, age, gender, height, weight) 
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

if ($stmt->execute([$name, $email, $password, $age, $gender, $height, $weight])) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["error" => "Failed to register user"]);
}
?>

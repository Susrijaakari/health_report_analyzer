<?php
require_once 'db.php';

// Generate a random token (can be replaced with JWT later)
function generateToken($userId) {
    $token = bin2hex(random_bytes(32));  // 64-character token
    $stmt = $GLOBALS['pdo']->prepare("INSERT INTO login_tokens (user_id, token, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$userId, $token]);
    return $token;
}

// Verify token and return user ID if valid
function verifyToken($token) {
    $stmt = $GLOBALS['pdo']->prepare("SELECT user_id FROM login_tokens WHERE token = ? LIMIT 1");
    $stmt->execute([$token]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['user_id'] : false;
}

// Optional: Invalidate token (logout)
function logoutToken($token) {
    $stmt = $GLOBALS['pdo']->prepare("DELETE FROM login_tokens WHERE token = ?");
    $stmt->execute([$token]);
}
?>

<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Method not allowed.', [], 405);
}

$body     = getJsonBody();
$name     = trim($body['name'] ?? '');
$email    = trim($body['email'] ?? '');
$password = $body['password'] ?? '';

if (!$name) {
    jsonResponse(false, 'Name is required.', [], 422);
}

if (!$email || !isValidEmail($email)) {
    jsonResponse(false, 'A valid email address is required.', [], 422);
}

if (strlen($password) < 8) {
    jsonResponse(false, 'Password must be at least 8 characters.', [], 422);
}

try {
    $pdo  = getConnection();
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        jsonResponse(false, 'An account with this email already exists.', [], 409);
    }

    $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
    $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);

    jsonResponse(true, 'Account created successfully.', [], 201);

} catch (PDOException $e) {
    error_log('DB error in register.php: ' . $e->getMessage());
    jsonResponse(false, 'A server error occurred.', [], 500);
}

<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Method not allowed.', [], 405);
}

$body    = getJsonBody();
$name    = trim($body['name']    ?? '');
$email   = trim($body['email']   ?? '');
$message = trim($body['message'] ?? '');

if (!$name) {
    jsonResponse(false, 'Name is required.', [], 422);
}

if (!$email || !isValidEmail($email)) {
    jsonResponse(false, 'A valid email address is required.', [], 422);
}

if (!$message) {
    jsonResponse(false, 'Message cannot be empty.', [], 422);
}

try {
    $pdo  = getConnection();
    $stmt = $pdo->prepare('INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)');
    $stmt->execute([$name, $email, $message]);

    jsonResponse(true, 'Message received. We will get back to you soon!', [], 201);

} catch (PDOException $e) {
    error_log('DB error in contact.php: ' . $e->getMessage());
    jsonResponse(false, 'A server error occurred.', [], 500);
}

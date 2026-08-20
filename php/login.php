<?php
ini_set('session.cookie_path', '/');
session_start();

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Method not allowed.', [], 405);
}

$body = getJsonBody();

$email    = trim($body['email'] ?? '');
$password = $body['password'] ?? '';

if (!$email || !isValidEmail($email)) {
    jsonResponse(false, 'A valid email address is required.', [], 422);
}

if (!$password) {
    jsonResponse(false, 'Password is required.', [], 422);
}

try {
    $pdo = getConnection();

    $stmt = $pdo->prepare('SELECT id, name, email, password, role FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        jsonResponse(false, 'Invalid email or password.', [], 401);
    }

    $_SESSION['user_id']   = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];

    jsonResponse(true, 'Logged in successfully.', [
        'user' => [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ]
    ]);

} catch (PDOException $e) {
    error_log('DB error in login.php: ' . $e->getMessage());
    jsonResponse(false, 'A server error occurred.', [], 500);
}

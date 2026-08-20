<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

header('Content-Type: application/json');

ini_set('session.cookie_path', '/');
session_start();

if (($_SESSION['user_role'] ?? '') !== 'admin') {
    jsonResponse(false, 'Forbidden.', [], 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Method not allowed.', [], 405);
}

$body    = getJsonBody();
$orderId = isset($body['order_id']) ? (int) $body['order_id'] : 0;
$status  = $body['status'] ?? '';
$allowed = ['pending', 'confirmed', 'completed', 'cancelled'];

if ($orderId <= 0) {
    jsonResponse(false, 'Invalid order ID.', [], 422);
}

if (!in_array($status, $allowed, true)) {
    jsonResponse(false, 'Invalid status value.', [], 422);
}

try {
    $pdo  = getConnection();
    $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
    $stmt->execute([$status, $orderId]);

    if ($stmt->rowCount() === 0) {
        jsonResponse(false, 'Order not found.', [], 404);
    }

    jsonResponse(true, 'Status updated.');

} catch (PDOException $e) {
    error_log('DB error in update_order_status.php: ' . $e->getMessage());
    jsonResponse(false, 'A server error occurred.', [], 500);
}

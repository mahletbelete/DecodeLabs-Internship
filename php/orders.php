<?php


require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

header('Content-Type: application/json');

// Start session once at the top — used by both placeOrder and getOrders
ini_set('session.cookie_path', '/');
session_start();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    placeOrder();
} elseif ($method === 'GET') {
    getOrders();
} else {
    jsonResponse(false, 'Method not allowed.', [], 405);
}

//  Place a new order

function placeOrder(): void {
    $body  = getJsonBody();
    $items = $body['items'] ?? [];

    if (empty($items) || !is_array($items)) {
        jsonResponse(false, 'Order must contain at least one item.', [], 422);
    }

    
    foreach ($items as $index => $item) {
        $productId = isset($item['product_id']) ? (int) $item['product_id'] : 0;
        $quantity  = isset($item['quantity'])   ? (int) $item['quantity']   : 0;
        $price     = isset($item['price'])      ? (float) $item['price']    : 0;

        if ($productId <= 0) {
            jsonResponse(false, "Item " . ($index + 1) . " has an invalid product ID.", [], 422);
        }
        if ($quantity <= 0) {
            jsonResponse(false, "Item " . ($index + 1) . " must have a quantity of at least 1.", [], 422);
        }
        if ($price <= 0) {
            jsonResponse(false, "Item " . ($index + 1) . " has an invalid price.", [], 422);
        }
    }

    try {
        $pdo = getConnection();

           foreach ($items as $item) {
            $stmt = $pdo->prepare('SELECT id, available FROM products WHERE id = ?');
            $stmt->execute([(int) $item['product_id']]);
            $product = $stmt->fetch();

            if (!$product) {
                jsonResponse(false, "Product ID {$item['product_id']} does not exist.", [], 422);
            }
            if (!$product['available']) {
                jsonResponse(false, "Product ID {$item['product_id']} is currently unavailable.", [], 422);
            }
        }

        $total = 0;
        foreach ($items as $item) {
            $stmt = $pdo->prepare('SELECT price FROM products WHERE id = ?');
            $stmt->execute([(int) $item['product_id']]);
            $row    = $stmt->fetch();
            $total += $row['price'] * (int) $item['quantity'];
        }

        $userId = $_SESSION['user_id'] ?? null;

        $pdo->beginTransaction();

        $stmt = $pdo->prepare(
            'INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, ?)'
        );
        $stmt->execute([$userId, $total, 'pending']);
        $orderId = (int) $pdo->lastInsertId();

        $itemStmt = $pdo->prepare(
            'INSERT INTO order_items (order_id, product_id, quantity, unit_price)
             VALUES (?, ?, ?, ?)'
        );

        foreach ($items as $item) {
           $stmt2 = $pdo->prepare('SELECT price FROM products WHERE id = ?');
            $stmt2->execute([(int) $item['product_id']]);
            $row = $stmt2->fetch();

            $itemStmt->execute([
                $orderId,
                (int) $item['product_id'],
                (int) $item['quantity'],
                $row['price']
            ]);
        }

        $pdo->commit();

        jsonResponse(true, 'Order placed successfully! Thank you.', ['order_id' => $orderId], 201);

    } catch (PDOException $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('DB error in orders.php: ' . $e->getMessage());
        jsonResponse(false, 'A server error occurred.', [], 500);
    }
}

// Retrieve orders 

function getOrders(): void {
    $userId = $_SESSION['user_id'] ?? null;
    $role   = $_SESSION['user_role'] ?? 'customer';

    try {
        $pdo = getConnection();

        if ($role === 'admin') {
        
            $stmt = $pdo->query(
                'SELECT o.*, u.name as customer_name
                 FROM orders o
                 LEFT JOIN users u ON o.user_id = u.id
                 ORDER BY o.created_at DESC'
            );
        } elseif ($userId) {
            $stmt = $pdo->prepare(
                'SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC'
            );
            $stmt->execute([$userId]);
        } else {
            jsonResponse(false, 'You must be logged in to view orders.', [], 401);
        }

        $orders = $stmt->fetchAll();

        foreach ($orders as &$order) {
            $itemStmt = $pdo->prepare(
                'SELECT oi.*, p.name as product_name
                 FROM order_items oi
                 JOIN products p ON oi.product_id = p.id
                 WHERE oi.order_id = ?'
            );
            $itemStmt->execute([$order['id']]);
            $order['items'] = $itemStmt->fetchAll();
        }

        jsonResponse(true, 'Orders retrieved.', ['orders' => $orders]);

    } catch (PDOException $e) {
        error_log('DB error in orders.php: ' . $e->getMessage());
        jsonResponse(false, 'A server error occurred.', [], 500);
    }
}

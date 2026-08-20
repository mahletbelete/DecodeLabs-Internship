<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int) $_GET['id'] : null;

try {
    $pdo = getConnection();

    if ($method === 'GET') {
        $id ? getProduct($pdo, $id) : getProducts($pdo);
    } elseif ($method === 'POST') {
        createProduct($pdo);
    } elseif ($method === 'PUT') {
        updateProduct($pdo, $id);
    } elseif ($method === 'DELETE') {
        deleteProduct($pdo, $id);
    } else {
        jsonResponse(false, 'Method not allowed.', [], 405);
    }

} catch (PDOException $e) {
    error_log('DB error in products.php: ' . $e->getMessage());
    jsonResponse(false, 'A server error occurred.', [], 500);
}

function getProducts(PDO $pdo): void {
    $stmt = $pdo->query('SELECT * FROM products ORDER BY category, name');
    jsonResponse(true, 'Products retrieved.', ['products' => $stmt->fetchAll()]);
}

function getProduct(PDO $pdo, int $id): void {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        jsonResponse(false, 'Product not found.', [], 404);
    }

    jsonResponse(true, 'Product retrieved.', ['product' => $product]);
}

function createProduct(PDO $pdo): void {
    $body        = getJsonBody();
    $name        = trim($body['name'] ?? '');
    $description = trim($body['description'] ?? '');
    $price       = $body['price'] ?? null;
    $category    = trim($body['category'] ?? '');
    $image       = trim($body['image'] ?? '');
    $available   = isset($body['available']) ? (int) $body['available'] : 1;

    if (!$name || !$price || !$category) {
        jsonResponse(false, 'Name, price, and category are required.', [], 422);
    }

    if (!is_numeric($price) || $price <= 0) {
        jsonResponse(false, 'Price must be a positive number.', [], 422);
    }

    $stmt = $pdo->prepare(
        'INSERT INTO products (name, description, price, category, image, available) VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$name, $description, $price, $category, $image, $available]);

    jsonResponse(true, 'Product created.', ['id' => (int) $pdo->lastInsertId()], 201);
}

function updateProduct(PDO $pdo, ?int $id): void {
    if (!$id) jsonResponse(false, 'Product ID is required.', [], 422);

    $body        = getJsonBody();
    $name        = trim($body['name'] ?? '');
    $description = trim($body['description'] ?? '');
    $price       = $body['price'] ?? null;
    $category    = trim($body['category'] ?? '');
    $image       = trim($body['image'] ?? '');
    $available   = isset($body['available']) ? (int) $body['available'] : 1;

    if (!$name || !$price || !$category) {
        jsonResponse(false, 'Name, price, and category are required.', [], 422);
    }

    if (!is_numeric($price) || $price <= 0) {
        jsonResponse(false, 'Price must be a positive number.', [], 422);
    }

    $stmt = $pdo->prepare(
        'UPDATE products SET name = ?, description = ?, price = ?, category = ?, image = ?, available = ? WHERE id = ?'
    );
    $stmt->execute([$name, $description, $price, $category, $image, $available, $id]);

    if ($stmt->rowCount() === 0) {
        jsonResponse(false, 'Product not found or nothing changed.', [], 404);
    }

    jsonResponse(true, 'Product updated.');
}

function deleteProduct(PDO $pdo, ?int $id): void {
    if (!$id) jsonResponse(false, 'Product ID is required.', [], 422);

    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        jsonResponse(false, 'Product not found.', [], 404);
    }

    jsonResponse(true, 'Product deleted.');
}

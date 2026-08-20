<?php

require_once __DIR__ . '/../php/db.php';

ini_set('session.cookie_path', '/');
session_start();

if (($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../login.html');
    exit;
}

$pdo = getConnection();

$stmt = $pdo->query(
    'SELECT o.*, u.name as customer_name, u.email as customer_email
     FROM orders o
     LEFT JOIN users u ON o.user_id = u.id
     ORDER BY o.created_at DESC'
);
$orders = $stmt->fetchAll();


foreach ($orders as &$order) {
    $itemStmt = $pdo->prepare(
        'SELECT oi.quantity, oi.unit_price, p.name as product_name
         FROM order_items oi
         JOIN products p ON oi.product_id = p.id
         WHERE oi.order_id = ?'
    );
    $itemStmt->execute([$order['id']]);
    $order['items'] = $itemStmt->fetchAll();
}
unset($order);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Orders — Admin MorningMug</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.0/css/all.min.css"
    integrity="sha512-ApSLB1Pd3/bZN8fWB/RG9YhN/7bd9Hkf3AGaE2mPfebjrxagjuBtx2GcgdqIlJkUzwylBo61r9Xa9NmgBI0swA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">

  <aside class="admin-sidebar">
    <a href="../index.html" class="admin-logo">
      <i class="fa-solid fa-mug-hot"></i>
      <span>MorningMug</span>
    </a>
    <nav class="admin-nav">
      <a href="index.php"    class="admin-nav-link"><i class="fa-solid fa-gauge"></i> Dashboard</a>
      <a href="products.php" class="admin-nav-link"><i class="fa-solid fa-mug-hot"></i> Products</a>
      <a href="orders.php"   class="admin-nav-link active"><i class="fa-solid fa-receipt"></i> Orders</a>
      <a href="messages.php" class="admin-nav-link"><i class="fa-solid fa-envelope"></i> Messages</a>
      <a href="../php/logout.php" class="admin-nav-link admin-logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>
  </aside>

  <main class="admin-main">
    <h1 class="admin-page-title">Orders</h1>

    <section class="admin-card">
      <div class="table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Customer</th>
              <th>Items</th>
              <th>Total</th>
              <th>Status</th>
              <th>Date</th>
              <th>Update Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
              <td>#<?= $order['id'] ?></td>
              <td>
                <?php if ($order['customer_name']): ?>
                  <?= htmlspecialchars($order['customer_name']) ?><br>
                  <small><?= htmlspecialchars($order['customer_email']) ?></small>
                <?php else: ?>
                  <em>Guest</em>
                <?php endif; ?>
              </td>
              <td>
                <ul class="order-items-list">
                  <?php foreach ($order['items'] as $item): ?>
                    <li><?= htmlspecialchars($item['product_name']) ?> × <?= $item['quantity'] ?></li>
                  <?php endforeach; ?>
                </ul>
              </td>
              <td>$<?= number_format($order['total_price'], 2) ?></td>
              <td>
                <?php
                $statusClass = match($order['status']) {
                    'pending'   => 'badge-yellow',
                    'confirmed' => 'badge-blue',
                    'completed' => 'badge-green',
                    'cancelled' => 'badge-red',
                    default     => ''
                };
                ?>
                <span class="badge <?= $statusClass ?>"><?= ucfirst($order['status']) ?></span>
              </td>
              <td><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></td>
              <td>
                <select class="status-select" data-order-id="<?= $order['id'] ?>">
                  <option value="pending"   <?= $order['status'] === 'pending'   ? 'selected' : '' ?>>Pending</option>
                  <option value="confirmed" <?= $order['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                  <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                  <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
                <button class="admin-btn admin-btn-small admin-btn-primary update-status-btn" data-order-id="<?= $order['id'] ?>">
                  Save
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <script src="orders.js"></script>
</body>
</html>

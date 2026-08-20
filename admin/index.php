<?php

require_once __DIR__ . '/../php/db.php';
require_once __DIR__ . '/../php/helpers.php';

ini_set('session.cookie_path', '/');
session_start();


if (($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../login.html');
    exit;
}

$pdo = getConnection();

$productCount = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$orderCount   = $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$userCount    = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$messageCount = $pdo->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin — MorningMug</title>
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
      <a href="index.php"    class="admin-nav-link active"><i class="fa-solid fa-gauge"></i> Dashboard</a>
      <a href="products.php" class="admin-nav-link"><i class="fa-solid fa-mug-hot"></i> Products</a>
      <a href="orders.php"   class="admin-nav-link"><i class="fa-solid fa-receipt"></i> Orders</a>
      <a href="messages.php" class="admin-nav-link"><i class="fa-solid fa-envelope"></i> Messages</a>
      <a href="../php/logout.php" class="admin-nav-link admin-logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>
  </aside>

  <main class="admin-main">
    <h1 class="admin-page-title">Dashboard</h1>
    <p class="admin-welcome">Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>.</p>

    <div class="admin-stats">
      <div class="stat-card">
        <i class="fa-solid fa-mug-hot stat-icon"></i>
        <div>
          <p class="stat-number"><?= $productCount ?></p>
          <p class="stat-label">Products</p>
        </div>
      </div>
      <div class="stat-card">
        <i class="fa-solid fa-receipt stat-icon"></i>
        <div>
          <p class="stat-number"><?= $orderCount ?></p>
          <p class="stat-label">Orders</p>
        </div>
      </div>
      <div class="stat-card">
        <i class="fa-solid fa-users stat-icon"></i>
        <div>
          <p class="stat-number"><?= $userCount ?></p>
          <p class="stat-label">Users</p>
        </div>
      </div>
      <div class="stat-card">
        <i class="fa-solid fa-envelope stat-icon"></i>
        <div>
          <p class="stat-number"><?= $messageCount ?></p>
          <p class="stat-label">Messages</p>
        </div>
      </div>
    </div>
  </main>

</body>
</html>

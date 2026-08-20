<?php

require_once __DIR__ . '/../php/db.php';

ini_set('session.cookie_path', '/');
session_start();

if (($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../login.html');
    exit;
}

$pdo = getConnection();
$messages = $pdo->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Messages — Admin MorningMug</title>
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
      <a href="orders.php"   class="admin-nav-link"><i class="fa-solid fa-receipt"></i> Orders</a>
      <a href="messages.php" class="admin-nav-link active"><i class="fa-solid fa-envelope"></i> Messages</a>
      <a href="../php/logout.php" class="admin-nav-link admin-logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>
  </aside>

  <main class="admin-main">
    <h1 class="admin-page-title">Contact Messages</h1>

    <section class="admin-card">
      <div class="table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Message</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($messages as $msg): ?>
            <tr>
              <td><?= $msg['id'] ?></td>
              <td><?= htmlspecialchars($msg['name']) ?></td>
              <td><?= htmlspecialchars($msg['email']) ?></td>
              <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
              <td><?= date('d M Y, H:i', strtotime($msg['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>

</body>
</html>

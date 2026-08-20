<?php

require_once __DIR__ . '/../php/db.php';

ini_set('session.cookie_path', '/');
session_start();

if (($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../login.html');
    exit;
}

$pdo = getConnection();
$stmt = $pdo->query('SELECT * FROM products ORDER BY category, name');
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products — Admin MorningMug</title>
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
      <a href="products.php" class="admin-nav-link active"><i class="fa-solid fa-mug-hot"></i> Products</a>
      <a href="orders.php"   class="admin-nav-link"><i class="fa-solid fa-receipt"></i> Orders</a>
      <a href="messages.php" class="admin-nav-link"><i class="fa-solid fa-envelope"></i> Messages</a>
      <a href="../php/logout.php" class="admin-nav-link admin-logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>
  </aside>

  <main class="admin-main">
    <h1 class="admin-page-title">Products</h1>

    <!-- CREATE / EDIT form -->
    <section class="admin-card" id="product-form-section">
      <h2 class="admin-card-title" id="form-heading">Add New Product</h2>

      <form class="admin-form" id="product-form">
        <input type="hidden" id="product-id">

        <div class="admin-form-row">
          <div class="form-group">
            <label for="p-name">Name</label>
            <input type="text" id="p-name" placeholder="e.g. Cappuccino" required>
            <span class="field-error" id="p-name-error"></span>
          </div>
          <div class="form-group">
            <label for="p-category">Category</label>
            <input type="text" id="p-category" placeholder="e.g. Coffee" required>
            <span class="field-error" id="p-category-error"></span>
          </div>
        </div>

        <div class="admin-form-row">
          <div class="form-group">
            <label for="p-price">Price ($)</label>
            <input type="number" id="p-price" step="0.01" min="0.01" placeholder="4.50" required>
            <span class="field-error" id="p-price-error"></span>
          </div>
          <div class="form-group">
            <label for="p-image">Image filename</label>
            <input type="text" id="p-image" placeholder="latte.jpg (optional)">
          </div>
        </div>

        <div class="form-group">
          <label for="p-description">Description</label>
          <textarea id="p-description" rows="2" placeholder="Short description..."></textarea>
        </div>

        <div class="form-group">
          <label class="checkbox-label">
            <input type="checkbox" id="p-available" checked>
            Available for ordering
          </label>
        </div>

        <p class="form-message" id="product-form-message"></p>

        <div class="admin-form-actions">
          <button type="submit" class="admin-btn admin-btn-primary" id="form-submit-btn">Add Product</button>
          <button type="button" class="admin-btn admin-btn-secondary" id="form-cancel-btn" style="display:none">Cancel</button>
        </div>
      </form>
    </section>

    <!-- READ — products table -->
    <section class="admin-card">
      <h2 class="admin-card-title">All Products</h2>
      <div class="table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Category</th>
              <th>Price</th>
              <th>Available</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="products-table-body">
            <?php foreach ($products as $p): ?>
            <tr id="row-<?= $p['id'] ?>">
              <td><?= $p['id'] ?></td>
              <td><?= htmlspecialchars($p['name']) ?></td>
              <td><?= htmlspecialchars($p['category']) ?></td>
              <td>$<?= number_format($p['price'], 2) ?></td>
              <td><?= $p['available'] ? '<span class="badge badge-green">Yes</span>' : '<span class="badge badge-red">No</span>' ?></td>
              <td class="action-cell">
                <button
                  class="admin-btn admin-btn-small admin-btn-secondary edit-btn"
                  data-id="<?= $p['id'] ?>"
                  data-name="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>"
                  data-description="<?= htmlspecialchars($p['description'] ?? '', ENT_QUOTES) ?>"
                  data-price="<?= $p['price'] ?>"
                  data-category="<?= htmlspecialchars($p['category'], ENT_QUOTES) ?>"
                  data-image="<?= htmlspecialchars($p['image'] ?? '', ENT_QUOTES) ?>"
                  data-available="<?= $p['available'] ?>"
                >
                  <i class="fa-solid fa-pen"></i> Edit
                </button>
                <button class="admin-btn admin-btn-small admin-btn-danger delete-btn" data-id="<?= $p['id'] ?>">
                  <i class="fa-solid fa-trash"></i> Delete
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

  </main>

  <script src="products.js"></script>
</body>
</html>

<?php
require_once __DIR__ . '/../../includes/admin_guard.php';
require_once __DIR__ . '/../../classes/Database.php';

$pdo = Database::conn();

$sql = "
SELECT p.id, p.name, p.price, p.discount_percent, p.stock, p.image, p.created_at,
       c.name AS category_name
FROM products p
LEFT JOIN categories c ON c.id = p.category_id
ORDER BY p.id DESC
";
$products = $pdo->query($sql)->fetchAll();

$pageTitle = "Products";
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="layout">
  <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

  <main class="content">
    <div class="topbar">
      <div>
        <strong>Products</strong><br>
        <span class="muted">إدارة منتجات الأحذية</span>
      </div>
      <div>
        <a class="btn" href="<?= $APP['BASE_URL'] ?>/admin/products/create.php">+ Add Product</a>
      </div>
    </div>

    <div class="container">
      <div class="card">
        <h3 style="margin:0 0 12px 0;">All Products</h3>

        <table style="width:100%; border-collapse:collapse;">
          <thead>
            <tr style="text-align:right;">
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">ID</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Image</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Name</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Category</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Price</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Discount%</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Stock</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($products)): ?>
              <tr><td colspan="8" class="muted" style="padding:12px;">No products yet.</td></tr>
            <?php else: ?>
              <?php foreach ($products as $p): ?>
                <tr>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= (int)$p['id'] ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;">
                    <?php if (!empty($p['image'])): ?>
                      <img src="<?= $APP['BASE_URL'] ?>/assets/uploads/products/<?= htmlspecialchars($p['image']) ?>"
                           style="width:50px; height:50px; object-fit:cover; border-radius:10px;">
                    <?php else: ?>
                      <span class="muted">—</span>
                    <?php endif; ?>
                  </td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= htmlspecialchars($p['name']) ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= htmlspecialchars($p['category_name'] ?? '—') ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= number_format((float)$p['price'], 2) ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= (int)$p['discount_percent'] ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= (int)$p['stock'] ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;">
                    <a class="btn" href="<?= $APP['BASE_URL'] ?>/admin/products/edit.php?id=<?= (int)$p['id'] ?>">Edit</a>
                    <a class="btn" href="<?= $APP['BASE_URL'] ?>/admin/products/delete.php?id=<?= (int)$p['id'] ?>"
                       onclick="return confirm('Delete this product?');">Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>

      </div>
    </div>
  </main>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

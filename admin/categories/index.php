<?php
require_once __DIR__ . '/../../includes/admin_guard.php';
require_once __DIR__ . '/../../classes/Database.php';

$pdo = Database::conn();
$stmt = $pdo->query("SELECT id, name, description, created_at FROM categories ORDER BY id DESC");
$categories = $stmt->fetchAll();

$pageTitle = "Categories";
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="layout">
  <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

  <main class="content">
    <div class="topbar">
      <div>
        <strong>Categories</strong><br>
        <span class="muted">إدارة تصنيفات الأحذية</span>
      </div>
      <div>
        <a class="btn" href="<?= $APP['BASE_URL'] ?>/admin/categories/create.php">+ Add Category</a>
      </div>
    </div>

    <div class="container">
      <div class="card">
        <h3 style="margin:0 0 12px 0;">All Categories</h3>

        <table style="width:100%; border-collapse:collapse;">
          <thead>
            <tr style="text-align:right;">
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">ID</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Name</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Description</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Created</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($categories)): ?>
              <tr>
                <td colspan="5" style="padding:12px;" class="muted">No categories yet.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($categories as $cat): ?>
                <tr>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= (int)$cat['id'] ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= htmlspecialchars($cat['name']) ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= htmlspecialchars($cat['description'] ?? '') ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= htmlspecialchars($cat['created_at']) ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;">
                    <a class="btn" href="<?= $APP['BASE_URL'] ?>/admin/categories/edit.php?id=<?= (int)$cat['id'] ?>">Edit</a>
                    <a class="btn" href="<?= $APP['BASE_URL'] ?>/admin/categories/delete.php?id=<?= (int)$cat['id'] ?>"
                       onclick="return confirm('Delete this category?');">Delete</a>
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

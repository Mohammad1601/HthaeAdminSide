<?php
require_once __DIR__ . '/../../includes/admin_guard.php';
require_once __DIR__ . '/../../classes/Database.php';

$pdo = Database::conn();

$sql = "
SELECT 
  r.id,
  r.rating,
  r.comment,
  r.is_approved,
  r.created_at,
  u.username,
  u.email,
  p.name AS product_name
FROM reviews r
JOIN users u ON u.id = r.user_id
JOIN products p ON p.id = r.product_id
ORDER BY r.id DESC
";
$reviews = $pdo->query($sql)->fetchAll();

$pageTitle = "Reviews";
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="layout">
  <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

  <main class="content">
    <div class="topbar">
      <div>
        <strong>Reviews</strong><br>
        <span class="muted">إدارة تقييمات وتعليقات المنتجات</span>
      </div>
    </div>

    <div class="container">
      <div class="card">
        <h3 style="margin:0 0 12px 0;">All Reviews</h3>

        <table style="width:100%; border-collapse:collapse;">
          <thead>
            <tr style="text-align:right;">
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">ID</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Product</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">User</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Rating</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Comment</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Approved</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Created</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($reviews)): ?>
              <tr><td colspan="8" class="muted" style="padding:12px;">No reviews yet.</td></tr>
            <?php else: ?>
              <?php foreach ($reviews as $r): ?>
                <tr>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= (int)$r['id'] ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= htmlspecialchars($r['product_name']) ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;">
                    <?= htmlspecialchars($r['username']) ?><br>
                    <span class="muted"><?= htmlspecialchars($r['email']) ?></span>
                  </td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= (int)$r['rating'] ?>/5</td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px; max-width:360px;">
                    <?= htmlspecialchars($r['comment'] ?? '') ?>
                  </td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;">
                    <?= ((int)$r['is_approved'] === 1) ? 'Yes' : 'No' ?>
                  </td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= htmlspecialchars($r['created_at']) ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px; white-space:nowrap;">
                    <?php if ((int)$r['is_approved'] === 1): ?>
                      <a class="btn" href="<?= $APP['BASE_URL'] ?>/admin/reviews/toggle.php?id=<?= (int)$r['id'] ?>&to=0">Reject</a>
                    <?php else: ?>
                      <a class="btn" href="<?= $APP['BASE_URL'] ?>/admin/reviews/toggle.php?id=<?= (int)$r['id'] ?>&to=1">Approve</a>
                    <?php endif; ?>

                    <a class="btn"
                       href="<?= $APP['BASE_URL'] ?>/admin/reviews/delete.php?id=<?= (int)$r['id'] ?>"
                       onclick="return confirm('Delete this review?');">Delete</a>
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

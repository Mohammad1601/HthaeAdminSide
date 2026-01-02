<?php
require_once __DIR__ . '/../../includes/admin_guard.php';
require_once __DIR__ . '/../../classes/Database.php';

$pdo = Database::conn();

$sql = "
SELECT 
  o.id,
  o.total_amount,
  o.status,
  o.created_at,
  u.username,
  u.email,
  p.payment_status
FROM orders o
JOIN users u ON u.id = o.user_id
LEFT JOIN payments p ON p.order_id = o.id
ORDER BY o.id DESC
";
$orders = $pdo->query($sql)->fetchAll();

$pageTitle = "Orders";
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="layout">
  <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

  <main class="content">
    <div class="topbar">
      <div>
        <strong>Orders</strong><br>
        <span class="muted">إدارة الطلبات</span>
      </div>
    </div>

    <div class="container">
      <div class="card">
        <h3 style="margin:0 0 12px 0;">All Orders</h3>

        <table style="width:100%; border-collapse:collapse;">
          <thead>
            <tr style="text-align:right;">
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Order #</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Customer</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Total</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Payment</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Status</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Created</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($orders)): ?>
              <tr><td colspan="7" class="muted" style="padding:12px;">No orders yet.</td></tr>
            <?php else: ?>
              <?php foreach ($orders as $o): ?>
                <tr>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= (int)$o['id'] ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;">
                    <?= htmlspecialchars($o['username']) ?><br>
                    <span class="muted"><?= htmlspecialchars($o['email']) ?></span>
                  </td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= number_format((float)$o['total_amount'], 2) ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;">
                    <?= htmlspecialchars($o['payment_status'] ?? 'unpaid') ?>
                  </td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= htmlspecialchars($o['status']) ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= htmlspecialchars($o['created_at']) ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;">
                    <a class="btn" href="<?= $APP['BASE_URL'] ?>/admin/orders/view.php?id=<?= (int)$o['id'] ?>">View</a>
                    <a class="btn" href="<?= $APP['BASE_URL'] ?>/admin/orders/delete.php?id=<?= (int)$o['id'] ?>"
                       onclick="return confirm('Delete this order?');">Delete</a>
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

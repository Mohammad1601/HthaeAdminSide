<?php
require_once __DIR__ . '/../../includes/admin_guard.php';
require_once __DIR__ . '/../../classes/Database.php';

$pdo = Database::conn();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: /HthaeAdminSide/admin/orders/index.php");
    exit;
}

$orderStmt = $pdo->prepare("
  SELECT o.*, u.username, u.email, p.payment_status
  FROM orders o
  JOIN users u ON u.id = o.user_id
  LEFT JOIN payments p ON p.order_id = o.id
  WHERE o.id = ?
");
$orderStmt->execute([$id]);
$order = $orderStmt->fetch();

if (!$order) {
    header("Location: /HthaeAdminSide/admin/orders/index.php");
    exit;
}

$itemsStmt = $pdo->prepare("
  SELECT id, product_name, unit_price, quantity, line_total
  FROM order_items
  WHERE order_id = ?
  ORDER BY id ASC
");
$itemsStmt->execute([$id]);
$items = $itemsStmt->fetchAll();

$paymentStmt = $pdo->prepare("
  SELECT cardholder_name, card_number, expiry_date, cvv, address, payment_status, created_at
  FROM payments
  WHERE order_id = ?
  LIMIT 1
");
$paymentStmt->execute([$id]);
$payment = $paymentStmt->fetch();

$pageTitle = "Order Details";
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="layout">
  <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

  <main class="content">
    <div class="topbar">
      <div>
        <strong>Order #<?= (int)$order['id'] ?></strong><br>
        <span class="muted">تفاصيل الطلب</span>
      </div>
      <div style="display:flex; gap:10px;">
        <a class="btn" href="<?= $APP['BASE_URL'] ?>/admin/orders/index.php">Back</a>
      </div>
    </div>

    <div class="container" style="display:grid; grid-template-columns: 1.2fr 0.8fr; gap:12px; align-items:start;">

      <div class="card">
        <h3 style="margin:0 0 10px 0;">Items</h3>

        <table style="width:100%; border-collapse:collapse;">
          <thead>
            <tr style="text-align:right;">
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Product</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Unit</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Qty</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Total</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($items)): ?>
              <tr><td colspan="4" class="muted" style="padding:12px;">No items for this order.</td></tr>
            <?php else: ?>
              <?php foreach ($items as $it): ?>
                <tr>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= htmlspecialchars($it['product_name']) ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= number_format((float)$it['unit_price'], 2) ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= (int)$it['quantity'] ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= number_format((float)$it['line_total'], 2) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>

        <div style="margin-top:12px; text-align:left;">
          <strong>Total: <?= number_format((float)$order['total_amount'], 2) ?></strong>
        </div>
      </div>

      <div style="display:flex; flex-direction:column; gap:12px;">
        <div class="card">
          <h3 style="margin:0 0 10px 0;">Order Info</h3>
          <div class="muted">Customer</div>
          <div><?= htmlspecialchars($order['username']) ?> <span class="muted">(<?= htmlspecialchars($order['email']) ?>)</span></div>

          <div style="margin-top:10px;" class="muted">Payment</div>
          <div><?= htmlspecialchars($order['payment_status'] ?? 'unpaid') ?></div>

          <div style="margin-top:10px;" class="muted">Status</div>
          <div><?= htmlspecialchars($order['status']) ?></div>

          <div style="margin-top:10px;" class="muted">Created</div>
          <div><?= htmlspecialchars($order['created_at']) ?></div>
        </div>

        <div class="card">
          <h3 style="margin:0 0 10px 0;">Update Status</h3>
          <form method="post" action="<?= $APP['BASE_URL'] ?>/admin/orders/update_status.php">
            <input type="hidden" name="id" value="<?= (int)$order['id'] ?>">
            <select name="status" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:10px;">
              <option value="pending"   <?= $order['status']==='pending' ? 'selected' : '' ?>>pending</option>
              <option value="approved"  <?= $order['status']==='approved' ? 'selected' : '' ?>>approved</option>
              <option value="delivered" <?= $order['status']==='delivered' ? 'selected' : '' ?>>delivered</option>
            </select>
            <button class="btn" type="submit" style="margin-top:10px;">Save</button>
          </form>
        </div>

        <div class="card">
          <h3 style="margin:0 0 10px 0;">Payment Details</h3>
          <?php if (!$payment): ?>
            <div class="muted">No payment record for this order.</div>
          <?php else: ?>
            <div class="muted">Cardholder</div>
            <div><?= htmlspecialchars($payment['cardholder_name']) ?></div>

            <div class="muted" style="margin-top:10px;">Card Number</div>
            <div><?= htmlspecialchars($payment['card_number']) ?></div>

            <div class="muted" style="margin-top:10px;">Expiry / CVV</div>
            <div><?= htmlspecialchars($payment['expiry_date']) ?> / <?= htmlspecialchars($payment['cvv']) ?></div>

            <div class="muted" style="margin-top:10px;">Address</div>
            <div><?= htmlspecialchars($payment['address']) ?></div>

            <div class="muted" style="margin-top:10px;">Status</div>
            <div><?= htmlspecialchars($payment['payment_status']) ?></div>

            <div class="muted" style="margin-top:10px;">Created</div>
            <div><?= htmlspecialchars($payment['created_at']) ?></div>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </main>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

<?php
require_once __DIR__ . '/../includes/admin_guard.php';
require_once __DIR__ . '/dashboard_data.php';

$pageTitle = "Admin Dashboard";
require_once __DIR__ . '/../includes/header.php';
?>
<div class="layout">
  <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

  <main class="content">
    <div class="topbar">
      <div>
        <strong>Dashboard</strong><br>
        <span class="muted">إحصاءات المتجر (أحذية)</span>
      </div>
      <div>
        <a class="btn" href="<?= $APP['BASE_URL'] ?>/admin/logout.php">تسجيل الخروج</a>
      </div>
    </div>

    <div class="container">

      <div style="display:grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap:12px;">
        <div class="card">
          <div class="muted">Total Income</div>
          <div style="font-size:22px; font-weight:700; margin-top:6px;">
            <?= number_format($totalIncome, 2) ?>
          </div>
        </div>

        <div class="card">
          <div class="muted">Categories</div>
          <div style="font-size:22px; font-weight:700; margin-top:6px;">
            <?= $categoriesCount ?>
          </div>
        </div>

        <div class="card">
          <div class="muted">Orders</div>
          <div style="font-size:22px; font-weight:700; margin-top:6px;">
            <?= $ordersCount ?>
          </div>
        </div>

        <div class="card">
          <div class="muted">Customers</div>
          <div style="font-size:22px; font-weight:700; margin-top:6px;">
            <?= $customersCount ?>
          </div>
        </div>
      </div>

      <div class="card" style="margin-top:14px;">
        <h3 style="margin:0 0 10px 0;">Daily Revenue (Last 14 days)</h3>
        <div class="muted" style="margin-bottom:10px;">
          يعتمد على الطلبات المدفوعة (payments = paid)
        </div>

        <table style="width:100%; border-collapse:collapse;">
          <thead>
            <tr style="text-align:right;">
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Date</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Orders</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Revenue</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($dailyRevenue)): ?>
              <tr>
                <td colspan="3" style="padding:12px;" class="muted">
                  لا يوجد بيانات مدفوعة بعد. (أضف Orders + Payments لتظهر هنا)
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($dailyRevenue as $row): ?>
                <tr>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= htmlspecialchars($row['day']) ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= (int)$row['orders_count'] ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= number_format((float)$row['revenue'], 2) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
  </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<?php
// includes/sidebar.php
?>
<aside class="sidebar">
  <h2 style="margin:0 0 14px 0;">لوحة الإدارة</h2>
  <div class="muted" style="color:#cbd5e1; margin-bottom:14px;">
    <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Admin' ?>
  </div>

  <nav>
  <a href="<?= $APP['BASE_URL'] ?>/admin/index.php">Dashboard</a>
  <a href="<?= $APP['BASE_URL'] ?>/admin/categories/index.php">Categories</a>
  <a href="<?= $APP['BASE_URL'] ?>/admin/products/index.php">Products</a>
 <a href="<?= $APP['BASE_URL'] ?>/admin/users/index.php">Users</a>
 <a href="<?= $APP['BASE_URL'] ?>/admin/orders/index.php">Orders</a>
 <a href="<?= $APP['BASE_URL'] ?>/admin/reviews/index.php">Reviews</a>
  <a href="<?= $APP['BASE_URL'] ?>/admin/logout.php">Logout</a>
</nav>



  <div class="muted" style="color:#94a3b8; margin-top:18px;">
    ملاحظة: الروابط التي لا تملك صفحات بعد سيتم بناؤها في الخطوات القادمة.
  </div>
</aside>

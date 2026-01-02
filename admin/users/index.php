<?php
require_once __DIR__ . '/../../includes/admin_guard.php';
require_once __DIR__ . '/../../classes/Database.php';

$pdo = Database::conn();

$users = $pdo->query("
  SELECT id, username, email, role, created_at
  FROM users
  ORDER BY id DESC
")->fetchAll();

$pageTitle = "Users";
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="layout">
  <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

  <main class="content">
    <div class="topbar">
      <div>
        <strong>Users</strong><br>
        <span class="muted">إدارة المستخدمين</span>
      </div>
    </div>

    <div class="container">
      <div class="card">
        <h3 style="margin:0 0 12px 0;">All Users</h3>

        <table style="width:100%; border-collapse:collapse;">
          <thead>
            <tr style="text-align:right;">
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">ID</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Username</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Email</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Role</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Created</th>
              <th style="border-bottom:1px solid #e5e7eb; padding:10px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($users)): ?>
              <tr><td colspan="6" class="muted" style="padding:12px;">No users found.</td></tr>
            <?php else: ?>
              <?php foreach ($users as $u): ?>
                <tr>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= (int)$u['id'] ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= htmlspecialchars($u['username']) ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= htmlspecialchars($u['email']) ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= htmlspecialchars($u['role']) ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;"><?= htmlspecialchars($u['created_at']) ?></td>
                  <td style="border-bottom:1px solid #f1f5f9; padding:10px;">
                    <a class="btn" href="<?= $APP['BASE_URL'] ?>/admin/users/edit.php?id=<?= (int)$u['id'] ?>">Edit Role</a>

                    <?php if (isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] !== (int)$u['id']): ?>
                      <a class="btn"
                         href="<?= $APP['BASE_URL'] ?>/admin/users/delete.php?id=<?= (int)$u['id'] ?>"
                         onclick="return confirm('Delete this user?');">Delete</a>
                    <?php else: ?>
                      <span class="muted">—</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>

        <div class="muted" style="margin-top:10px;">
          ملاحظة: لا يمكن حذف المستخدم الحالي (الذي أنت مسجّل به).
        </div>
      </div>
    </div>
  </main>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

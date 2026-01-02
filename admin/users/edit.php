<?php
require_once __DIR__ . '/../../includes/admin_guard.php';
require_once __DIR__ . '/../../classes/Database.php';

$pdo = Database::conn();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: /HthaeAdminSide/admin/users/index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT id, username, email, role FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: /HthaeAdminSide/admin/users/index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? '';
    if (!in_array($role, ['admin', 'user'], true)) {
        $error = 'Invalid role.';
    } else {
        // لا تسمح بأن تجعل نفسك user (حتى لا تطرد نفسك من الأدمن)
        if (isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === $id && $role !== 'admin') {
            $error = 'You cannot change your own role to user.';
        } else {
            $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->execute([$role, $id]);

            header("Location: /HthaeAdminSide/admin/users/index.php");
            exit;
        }
    }
}

$pageTitle = "Edit User Role";
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="layout">
  <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

  <main class="content">
    <div class="topbar">
      <div>
        <strong>Edit Role</strong><br>
        <span class="muted">تعديل صلاحية المستخدم #<?= (int)$user['id'] ?></span>
      </div>
      <div>
        <a class="btn" href="<?= $APP['BASE_URL'] ?>/admin/users/index.php">Back</a>
      </div>
    </div>

    <div class="container">
      <div class="card" style="max-width:640px;">
        <?php if ($error): ?>
          <div class="muted" style="color:#b91c1c; margin-bottom:10px;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="muted">Username</div>
        <div style="margin-bottom:10px;"><?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars($user['email']) ?>)</div>

        <form method="post">
          <label style="display:block; margin-bottom:6px;">Role</label>
          <select name="role" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:10px;">
            <option value="user"  <?= $user['role']==='user' ? 'selected' : '' ?>>user</option>
            <option value="admin" <?= $user['role']==='admin' ? 'selected' : '' ?>>admin</option>
          </select>

          <div style="display:flex; gap:10px; margin-top:12px;">
            <button type="submit" class="btn">Save</button>
            <a class="btn" href="<?= $APP['BASE_URL'] ?>/admin/users/index.php">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </main>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

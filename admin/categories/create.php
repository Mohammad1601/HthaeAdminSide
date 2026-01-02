<?php
require_once __DIR__ . '/../../includes/admin_guard.php';
require_once __DIR__ . '/../../classes/Database.php';

$pdo = Database::conn();

$error = '';
$name = '';
$description = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($name === '') {
        $error = 'Name is required.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        $stmt->execute([$name, $description === '' ? null : $description]);

        header("Location: /HthaeAdminSide/admin/categories/index.php");
        exit;
    }
}

$pageTitle = "Add Category";
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="layout">
  <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

  <main class="content">
    <div class="topbar">
      <div>
        <strong>Add Category</strong><br>
        <span class="muted">إنشاء تصنيف جديد</span>
      </div>
      <div>
        <a class="btn" href="<?= $APP['BASE_URL'] ?>/admin/categories/index.php">Back</a>
      </div>
    </div>

    <div class="container">
      <div class="card" style="max-width:640px;">
        <?php if ($error): ?>
          <div class="muted" style="color:#b91c1c; margin-bottom:10px;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
          <label style="display:block; margin-bottom:6px;">Name *</label>
          <input name="name" value="<?= htmlspecialchars($name) ?>" required
                 style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:10px;">

          <label style="display:block; margin:12px 0 6px;">Description</label>
          <textarea name="description" rows="4"
                    style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:10px;"><?= htmlspecialchars($description) ?></textarea>

          <button type="submit" class="btn" style="margin-top:12px;">Create</button>
        </form>
      </div>
    </div>
  </main>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

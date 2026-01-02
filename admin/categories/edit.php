<?php
require_once __DIR__ . '/../../includes/admin_guard.php';
require_once __DIR__ . '/../../classes/Database.php';

$pdo = Database::conn();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: /HthaeAdminSide/admin/categories/index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT id, name, description FROM categories WHERE id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch();

if (!$category) {
    header("Location: /HthaeAdminSide/admin/categories/index.php");
    exit;
}

$error = '';
$name = $category['name'];
$description = $category['description'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($name === '') {
        $error = 'Name is required.';
    } else {
        $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
        $stmt->execute([$name, $description === '' ? null : $description, $id]);

        header("Location: /HthaeAdminSide/admin/categories/index.php");
        exit;
    }
}

$pageTitle = "Edit Category";
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="layout">
  <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

  <main class="content">
    <div class="topbar">
      <div>
        <strong>Edit Category</strong><br>
        <span class="muted">تعديل التصنيف #<?= (int)$id ?></span>
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

          <button>
           <div style="display:flex; gap:10px; margin-top:12px;">
            <button type="submit" class="btn">Save</button>
            <a class="btn" href="<?= $APP['BASE_URL'] ?>/admin/categories/index.php">Cancel</a>
           </div>
        </form>
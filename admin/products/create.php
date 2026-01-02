<?php
require_once __DIR__ . '/../../includes/admin_guard.php';
require_once __DIR__ . '/../../classes/Database.php';

$pdo = Database::conn();
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll();

$error = '';
$name = $description = '';
$price = '0';
$discount = '0';
$stock = '0';
$category_id = '';

function uploadProductImage(array $file, string $uploadDir): ?string {
    if (empty($file['name']) || $file['error'] !== UPLOAD_ERR_OK) return null;

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = mime_content_type($file['tmp_name']);
    if (!isset($allowed[$mime])) return null;

    if ($file['size'] > 2 * 1024 * 1024) return null; // 2MB

    $ext = $allowed[$mime];
    $newName = uniqid('p_', true) . '.' . $ext;

    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $dest = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $newName;

    return move_uploaded_file($file['tmp_name'], $dest) ? $newName : null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '0');
    $discount = trim($_POST['discount_percent'] ?? '0');
    $stock = trim($_POST['stock'] ?? '0');
    $category_id = trim($_POST['category_id'] ?? '');

    if ($name === '') $error = 'Name is required.';
    elseif (!is_numeric($price) || (float)$price < 0) $error = 'Price must be valid.';
    elseif (!ctype_digit($discount) || (int)$discount < 0 || (int)$discount > 100) $error = 'Discount must be 0..100.';
    elseif (!ctype_digit($stock) || (int)$stock < 0) $error = 'Stock must be 0 or more.';

    if ($error === '') {
        $uploadDir = __DIR__ . '/../../assets/uploads/products';
        $imageName = null;

        if (isset($_FILES['image'])) {
            $imageName = uploadProductImage($_FILES['image'], $uploadDir);
            // إذا فشل الرفع، نخليه null بدون تعطيل
        }

        $cid = ($category_id === '') ? null : (int)$category_id;

        $stmt = $pdo->prepare("
          INSERT INTO products (name, description, price, discount_percent, category_id, image, stock)
          VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
          $name,
          $description === '' ? null : $description,
          (float)$price,
          (int)$discount,
          $cid,
          $imageName,
          (int)$stock
        ]);

        header("Location: /HthaeAdminSide/admin/products/index.php");
        exit;
    }
}

$pageTitle = "Add Product";
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="layout">
  <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

  <main class="content">
    <div class="topbar">
      <div>
        <strong>Add Product</strong><br>
        <span class="muted">إضافة منتج (حذاء)</span>
      </div>
      <div>
        <a class="btn" href="<?= $APP['BASE_URL'] ?>/admin/products/index.php">Back</a>
      </div>
    </div>

    <div class="container">
      <div class="card" style="max-width:720px;">
        <?php if ($error): ?>
          <div class="muted" style="color:#b91c1c; margin-bottom:10px;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
          <label style="display:block; margin-bottom:6px;">Name *</label>
          <input name="name" value="<?= htmlspecialchars($name) ?>" required
                 style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:10px;">

          <label style="display:block; margin:12px 0 6px;">Description</label>
          <textarea name="description" rows="4"
            style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:10px;"><?= htmlspecialchars($description) ?></textarea>

          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-top:12px;">
            <div>
              <label style="display:block; margin-bottom:6px;">Price *</label>
              <input name="price" value="<?= htmlspecialchars($price) ?>" required
                     style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:10px;">
            </div>
            <div>
              <label style="display:block; margin-bottom:6px;">Discount %</label>
              <input name="discount_percent" value="<?= htmlspecialchars($discount) ?>"
                     style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:10px;">
            </div>
          </div>

          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-top:12px;">
            <div>
              <label style="display:block; margin-bottom:6px;">Category</label>
              <select name="category_id"
                      style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:10px;">
                <option value="">— None —</option>
                <?php foreach ($categories as $c): ?>
                  <option value="<?= (int)$c['id'] ?>" <?= ($category_id == $c['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label style="display:block; margin-bottom:6px;">Stock</label>
              <input name="stock" value="<?= htmlspecialchars($stock) ?>"
                     style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:10px;">
            </div>
          </div>

          <label style="display:block; margin:12px 0 6px;">Image</label>
          <input type="file" name="image" accept="image/*">

          <div style="display:flex; gap:10px; margin-top:12px;">
            <button type="submit" class="btn">Create</button>
            <a class="btn" href="<?= $APP['BASE_URL'] ?>/admin/products/index.php">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </main>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

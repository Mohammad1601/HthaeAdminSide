<?php
require_once __DIR__ . '/../../includes/admin_guard.php';
require_once __DIR__ . '/../../classes/Database.php';

$pdo = Database::conn();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: /HthaeAdminSide/admin/products/index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: /HthaeAdminSide/admin/products/index.php");
    exit;
}

$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll();

$error = '';
$name = $product['name'];
$description = $product['description'] ?? '';
$price = (string)$product['price'];
$discount = (string)$product['discount_percent'];
$stock = (string)$product['stock'];
$category_id = $product['category_id'];
$currentImage = $product['image'];

function uploadProductImage(array $file, string $uploadDir): ?string {
    if (empty($file['name']) || $file['error'] !== UPLOAD_ERR_OK) return null;
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = mime_content_type($file['tmp_name']);
    if (!isset($allowed[$mime])) return null;
    if ($file['size'] > 2 * 1024 * 1024) return null;
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
        $imageName = $currentImage;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $newImage = uploadProductImage($_FILES['image'], $uploadDir);
            if ($newImage) {
                if ($currentImage) {
                    $oldPath = $uploadDir . DIRECTORY_SEPARATOR . $currentImage;
                    if (is_file($oldPath)) @unlink($oldPath);
                }
                $imageName = $newImage;
            }
        }

        $cid = ($category_id === '') ? null : (int)$category_id;

        $stmt = $pdo->prepare("
          UPDATE products
          SET name=?, description=?, price=?, discount_percent=?, category_id=?, image=?, stock=?
          WHERE id=?
        ");
        $stmt->execute([
          $name,
          $description === '' ? null : $description,
          (float)$price,
          (int)$discount,
          $cid,
          $imageName,
          (int)$stock,
          $id
        ]);

        header("Location: /HthaeAdminSide/admin/products/index.php");
        exit;
    }
}

$pageTitle = "Edit Product";
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="layout">
  <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

  <main class="content">
    <div class="topbar">
      <div>
        <strong>Edit Product</strong><br>
        <span class="muted">تعديل المنتج #<?= (int)$id ?></span>
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
          <label>Name *</label>
          <input name="name" value="<?= htmlspecialchars($name) ?>" required style="width:100%; padding:10px;">

          <label style="margin-top:10px;">Description</label>
          <textarea name="description" rows="4" style="width:100%; padding:10px;"><?= htmlspecialchars($description) ?></textarea>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:10px;">
            <div>
              <label>Price *</label>
              <input name="price" value="<?= htmlspecialchars($price) ?>" required style="width:100%; padding:10px;">
            </div>
            <div>
              <label>Discount %</label>
              <input name="discount_percent" value="<?= htmlspecialchars($discount) ?>" style="width:100%; padding:10px;">
            </div>
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:10px;">
            <div>
              <label>Category</label>
              <select name="category_id" style="width:100%; padding:10px;">
                <option value="">— None —</option>
                <?php foreach ($categories as $c): ?>
                  <option value="<?= (int)$c['id'] ?>" <?= ($category_id == $c['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label>Stock</label>
              <input name="stock" value="<?= htmlspecialchars($stock) ?>" style="width:100%; padding:10px;">
            </div>
          </div>

          <label style="margin-top:10px;">Image</label>
          <input type="file" name="image" accept="image/*">
          <?php if ($currentImage): ?>
            <div class="muted" style="margin-top:6px;">Current: <?= htmlspecialchars($currentImage) ?></div>
          <?php endif; ?>

          <div style="display:flex; gap:10px; margin-top:12px;">
            <button type="submit" class="btn">Save</button>
            <a class="btn" href="<?= $APP['BASE_URL'] ?>/admin/products/index.php">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </main>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

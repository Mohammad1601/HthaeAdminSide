<?php
require_once __DIR__ . '/../../includes/admin_guard.php';
require_once __DIR__ . '/../../classes/Database.php';

$pdo = Database::conn();

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$status = $_POST['status'] ?? '';

$allowed = ['pending', 'approved', 'delivered'];
if ($id <= 0 || !in_array($status, $allowed, true)) {
    header("Location: /HthaeAdminSide/admin/orders/index.php");
    exit;
}

$stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
$stmt->execute([$status, $id]);

header("Location: /HthaeAdminSide/admin/orders/view.php?id=" . $id);
exit;

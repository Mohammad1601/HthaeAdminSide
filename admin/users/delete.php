<?php
require_once __DIR__ . '/../../includes/admin_guard.php';
require_once __DIR__ . '/../../classes/Database.php';

$pdo = Database::conn();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// امنع حذف نفسك
if ($id > 0 && isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] !== $id) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: /HthaeAdminSide/admin/users/index.php");
exit;

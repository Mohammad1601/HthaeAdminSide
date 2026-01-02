<?php
require_once __DIR__ . '/../../includes/admin_guard.php';
require_once __DIR__ . '/../../classes/Database.php';

$pdo = Database::conn();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$to = isset($_GET['to']) ? (int)$_GET['to'] : -1;

if ($id <= 0 || !in_array($to, [0, 1], true)) {
    header("Location: /HthaeAdminSide/admin/reviews/index.php");
    exit;
}

$stmt = $pdo->prepare("UPDATE reviews SET is_approved = ? WHERE id = ?");
$stmt->execute([$to, $id]);

header("Location: /HthaeAdminSide/admin/reviews/index.php");
exit;

<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$APP = require __DIR__ . '/../config/app.php';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Admin Panel' ?></title>

  <!-- Minimal styling for now (we will upgrade in later steps) -->
  <style>
    body { margin:0; font-family: Arial, sans-serif; background:#f5f5f5; }
    .layout { display:flex; min-height:100vh; }
    .sidebar { width:260px; background:#111827; color:#fff; padding:16px; }
    .sidebar a { display:block; color:#fff; text-decoration:none; padding:10px 8px; border-radius:8px; margin-bottom:6px; }
    .sidebar a:hover { background:#1f2937; }
    .content { flex:1; display:flex; flex-direction:column; }
    .topbar { background:#fff; padding:14px 18px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; }
    .container { padding:18px; }
    .card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px; }
    .muted { color:#6b7280; font-size:14px; }
    .btn { display:inline-block; padding:10px 14px; border-radius:10px; text-decoration:none; border:1px solid #d1d5db; background:#fff; color:#111827; }
    .btn:hover { background:#f9fafb; }
  </style>
</head>
<body>

<?php
// auth/login.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../classes/Database.php';
$APP = require __DIR__ . '/../config/app.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Please enter username and password.';
    } else {
        $pdo = Database::conn();

        $stmt = $pdo->prepare("
            SELECT id, username, password, role
            FROM users
            WHERE username = ?
            LIMIT 1
        ");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

  
        if ($user && $password === $user['password']) {
         
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

           
            if ($user['role'] === 'admin') {
                header("Location: {$APP['BASE_URL']}/admin/index.php");
            } else {
               
                header("Location: {$APP['BASE_URL']}/");
            }
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .card {
      background: #fff;
      padding: 24px;
      border-radius: 12px;
      width: 360px;
      border: 1px solid #e5e7eb;
    }
    h2 {
      margin-top: 0;
      margin-bottom: 16px;
      text-align: center;
    }
    label {
      display: block;
      margin-bottom: 6px;
    }
    input {
      width: 100%;
      padding: 10px;
      margin-bottom: 12px;
      border-radius: 8px;
      border: 1px solid #d1d5db;
    }
    .btn {
      width: 100%;
      padding: 10px;
      border-radius: 10px;
      border: 1px solid #d1d5db;
      background: #111827;
      color: #fff;
      cursor: pointer;
    }
    .btn:hover {
      background: #1f2937;
    }
    .error {
      color: #b91c1c;
      margin-bottom: 10px;
      text-align: center;
    }
  </style>
</head>
<body>

<div class="card">
  <h2>Admin Login</h2>

  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post">
    <label>Username</label>
    <input type="text" name="username" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <button class="btn" type="submit">Login</button>
  </form>
</div>

</body>
</html>

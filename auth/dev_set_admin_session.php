<?php
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'Admin';
$_SESSION['role'] = 'admin';

echo "Admin session set. Go to /admin/index.php";

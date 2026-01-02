<?php
// includes/admin_guard.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// REQUIRE: these session keys to allow admin access
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin    = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

if (!$isLoggedIn || !$isAdmin) {
    // Redirect to the public login page
  header("Location: /HthaeAdminSide/auth/login.php");


    exit;
}

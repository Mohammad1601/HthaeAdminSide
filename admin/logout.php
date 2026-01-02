<?php
// admin/logout.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
session_unset();
session_destroy();

header("Location: /HthaeAdminSide/auth/login.php");

exit;

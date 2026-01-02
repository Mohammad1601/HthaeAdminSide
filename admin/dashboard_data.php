<?php
require_once __DIR__ . '/../includes/admin_guard.php';
require_once __DIR__ . '/../classes/Database.php';

$pdo = Database::conn();

/* Total Income */
$stmt = $pdo->query("
    SELECT COALESCE(SUM(o.total_amount), 0) AS total
    FROM orders o
    INNER JOIN payments p ON p.order_id = o.id
    WHERE p.payment_status = 'paid'
");
$totalIncome = $stmt->fetch()['total'];

/* Counts */
$categoriesCount = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$ordersCount     = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$customersCount  = $pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();

/* Daily Revenue */
$stmt = $pdo->query("
    SELECT 
        DATE(o.created_at) AS day,
        COUNT(o.id) AS orders_count,
        SUM(o.total_amount) AS revenue
    FROM orders o
    INNER JOIN payments p ON p.order_id = o.id
    WHERE p.payment_status = 'paid'
    GROUP BY DATE(o.created_at)
    ORDER BY day DESC
    LIMIT 7
");
$dailyRevenue = $stmt->fetchAll();


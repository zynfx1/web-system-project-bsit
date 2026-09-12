<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$pdo = getDbConnection();

// Low Stock Query
$lowStockStmt = $pdo->query('
    SELECT product_name, stock_quantity, reorder_level
    FROM products
    WHERE stock_quantity <= reorder_level
    ORDER BY stock_quantity ASC
');
$lowStock = $lowStockStmt->fetchAll();

// Fast-Moving Products Query
$fastMovingStmt = $pdo->query('
    SELECT p.product_name, SUM(td.quantity) AS total_units_sold
    FROM transaction_details td
    JOIN products p ON td.product_id = p.product_id
    GROUP BY p.product_id, p.product_name
    ORDER BY total_units_sold DESC
    LIMIT 5
');
$fastMoving = $fastMovingStmt->fetchAll();

// Top Revenue Generating Products Query
$topRevenueStmt = $pdo->query('
    SELECT p.product_name, SUM(td.subtotal) AS total_revenue
    FROM transaction_details td
    JOIN products p ON td.product_id = p.product_id
    GROUP BY p.product_id, p.product_name
    ORDER BY total_revenue DESC
    LIMIT 5
');
$topRevenue = $topRevenueStmt->fetchAll();

// Total Store Revenue Query
$totalRevenueStmt = $pdo->query('SELECT COALESCE(SUM(total_amount), 0) AS total_sales_revenue FROM sales');
$totalRevenue = (float)$totalRevenueStmt->fetchColumn();

echo json_encode([
    'success' => true,
    'analytics' => [
        'total_revenue'        => $totalRevenue,
        'low_stock_alerts'     => $lowStock,
        'fast_moving'          => $fastMoving,
        'top_revenue_products' => $topRevenue,
    ],
]);
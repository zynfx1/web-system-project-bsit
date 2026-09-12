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
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->query('
        SELECT p.*, c.category_name
        FROM products p
        JOIN categories c ON p.category_id = c.category_id
        ORDER BY p.product_name ASC
    ');
    echo json_encode(['success' => true, 'products' => $stmt->fetchAll()]);
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];

    $categoryId   = (int)($input['category_id'] ?? 0);
    $productName  = trim((string)($input['product_name'] ?? ''));
    $costPrice    = (float)($input['cost_price'] ?? 0);
    $sellingPrice = (float)($input['selling_price'] ?? 0);
    $stockQty     = (int)($input['stock_quantity'] ?? 0);
    $reorderLevel = (int)($input['reorder_level'] ?? 10);

    if ($categoryId <= 0 || $productName === '' || $sellingPrice <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid product details.']);
        exit;
    }

    $stmt = $pdo->prepare('
        INSERT INTO products (category_id, product_name, cost_price, selling_price, stock_quantity, reorder_level)
        VALUES (:cid, :name, :cost, :sell, :stock, :reorder)
    ');
    $stmt->execute([
        'cid' => $categoryId,
        'name' => $productName,
        'cost' => $costPrice,
        'sell' => $sellingPrice,
        'stock' => $stockQty,
        'reorder' => $reorderLevel,
    ]);

    echo json_encode(['success' => true, 'message' => 'Product saved.']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
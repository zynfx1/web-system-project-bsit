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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$cart  = $input['cart'] ?? [];

if (empty($cart) || !is_array($cart)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Cart is empty.']);
    exit;
}

$pdo = getDbConnection();

try {
    $pdo->beginTransaction();

    $totalAmount = 0.0;
    $itemsToProcess = [];

    foreach ($cart as $item) {
        $pid = (int)($item['product_id'] ?? 0);
        $qty = (int)($item['quantity'] ?? 0);

        if ($pid <= 0 || $qty <= 0) {
            throw new Exception('Invalid item in transaction payload.');
        }

        $stmt = $pdo->prepare('
            SELECT product_id, product_name, selling_price, stock_quantity
            FROM products WHERE product_id = :id FOR UPDATE
        ');
        $stmt->execute(['id' => $pid]);
        $product = $stmt->fetch();

        if (!$product) {
            throw new Exception("Product ID {$pid} does not exist.");
        }

        if ($product['stock_quantity'] < $qty) {
            throw new Exception("Not enough stock for '{$product['product_name']}'. In stock: {$product['stock_quantity']}");
        }

        $unitPrice = (float)$product['selling_price'];
        $subtotal  = $unitPrice * $qty;
        $totalAmount += $subtotal;

        $itemsToProcess[] = [
            'product_id' => $pid,
            'quantity'   => $qty,
            'unit_price' => $unitPrice,
            'subtotal'   => $subtotal,
            'new_stock'  => $product['stock_quantity'] - $qty,
        ];
    }

    $txnNumber = 'SR-' . strtoupper(dechex((int)(microtime(true) * 1000)));

    $stmtSale = $pdo->prepare('INSERT INTO sales (transaction_number, total_amount) VALUES (:num, :total)');
    $stmtSale->execute(['num' => $txnNumber, 'total' => $totalAmount]);
    $transactionId = (int)$pdo->lastInsertId();

    $stmtDetail = $pdo->prepare('
        INSERT INTO transaction_details (transaction_id, product_id, quantity, unit_price, subtotal)
        VALUES (:tid, :pid, :qty, :price, :subtotal)
    ');

    $stmtUpdateStock = $pdo->prepare('UPDATE products SET stock_quantity = :stock WHERE product_id = :pid');

    foreach ($itemsToProcess as $i) {
        $stmtDetail->execute([
            'tid'      => $transactionId,
            'pid'      => $i['product_id'],
            'qty'      => $i['quantity'],
            'price'    => $i['unit_price'],
            'subtotal' => $i['subtotal'],
        ]);

        $stmtUpdateStock->execute([
            'stock' => $i['new_stock'],
            'pid'   => $i['product_id'],
        ]);
    }

    $pdo->commit();

    echo json_encode([
        'success'            => true,
        'message'            => 'Transaction complete.',
        'transaction_number' => $txnNumber,
        'total_amount'       => $totalAmount,
    ]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not signed in.']);
    exit;
}

$pdo = getDbConnection();
$stmt = $pdo->prepare('SELECT id, username, email, created_at FROM users WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

if ($user === false) {
    session_destroy();
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Session no longer valid.']);
    exit;
}

echo json_encode(['success' => true, 'user' => $user]);
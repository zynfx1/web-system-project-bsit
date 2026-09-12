<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];

$identifier = trim((string)($input['identifier'] ?? '')); // username OR email
$password   = (string)($input['password'] ?? '');

if ($identifier === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Enter your username/email and password.']);
    exit;
}

$pdo = getDbConnection();

$stmt = $pdo->prepare(
    'SELECT id, username, email, password_hash FROM users WHERE username = :username OR email = :email LIMIT 1'
);
$stmt->execute(['username' => $identifier, 'email' => $identifier]);
$user = $stmt->fetch();

if ($user === false || !password_verify($password, $user['password_hash'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => "Your username/email or password didn't match."]);
    exit;
}

// Prevent session fixation by issuing a fresh session id on privilege change.
session_regenerate_id(true);
$_SESSION['user_id']  = $user['id'];
$_SESSION['username'] = $user['username'];

echo json_encode([
    'success' => true,
    'message' => 'Signed in.',
    'user' => [
        'id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
    ],
]);
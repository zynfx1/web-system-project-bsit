<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];

$username = trim((string)($input['username'] ?? ''));
$email    = trim((string)($input['email'] ?? ''));
$password = (string)($input['password'] ?? '');

$errors = [];

if (mb_strlen($username) < 3 || mb_strlen($username) > 50) {
    $errors[] = 'Username must be 3-50 characters.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Enter a valid email address.';
}
if (mb_strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters.';
}

if ($errors !== []) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

$pdo = getDbConnection();

$check = $pdo->prepare('SELECT id FROM users WHERE username = :username OR email = :email LIMIT 1');
$check->execute(['username' => $username, 'email' => $email]);

if ($check->fetch() !== false) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'That username or email is already taken.']);
    exit;
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$insert = $pdo->prepare(
    'INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)'
);
$insert->execute([
    'username' => $username,
    'email' => $email,
    'password_hash' => $passwordHash,
]);

echo json_encode(['success' => true, 'message' => 'Account created. You can sign in now.']);
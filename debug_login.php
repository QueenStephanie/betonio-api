<?php

declare(strict_types=1);

require_once __DIR__ . '/db_connection.php';

$email = 'test@school.edu';
$password = 'password123';

// Get user from database
$sql = 'SELECT id, email, password, firstname, lastname FROM users WHERE email = ?';
$stmt = $connection->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result ? $result->fetch_assoc() : null;

if (!$user) {
  echo json_encode([
    'status' => 'error',
    'message' => 'User not found in database'
  ]);
  exit;
}

echo json_encode([
  'status' => 'debug',
  'user_exists' => true,
  'user_id' => $user['id'],
  'email' => $user['email'],
  'firstname' => $user['firstname'],
  'lastname' => $user['lastname'],
  'hashed_password' => $user['password'],
  'plain_password' => $password,
  'password_verify_result' => password_verify($password, $user['password']),
  'php_version' => phpversion(),
  'test_hash' => password_hash($password, PASSWORD_BCRYPT)
], JSON_PRETTY_PRINT);

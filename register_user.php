<?php

declare(strict_types=1);

require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/api_response.php';

set_api_headers();
handle_preflight();

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
  send_error('Method not allowed.', null, 405);
}

$input = read_json_input();
$email = trim((string) ($input['email'] ?? ''));
$password = trim((string) ($input['password'] ?? ''));
$firstname = trim((string) ($input['firstname'] ?? ''));
$lastname = trim((string) ($input['lastname'] ?? ''));

$errors = [];
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $errors['email'] = 'Valid email is required.';
}
if ($password === '' || strlen($password) < 6) {
  $errors['password'] = 'Password must be at least 6 characters.';
}
if ($firstname === '') {
  $errors['firstname'] = 'First name is required.';
}
if ($lastname === '') {
  $errors['lastname'] = 'Last name is required.';
}

if (!empty($errors)) {
  send_error('Failed to register user.', $errors, 422);
}

try {
  // Check if email already exists
  $checkSql = 'SELECT id FROM users WHERE email = ?';
  $checkStmt = $connection->prepare($checkSql);

  if (!$checkStmt) {
    send_error('Database error.', ['detail' => $connection->error], 500);
  }

  $checkStmt->bind_param('s', $email);
  $checkStmt->execute();
  $checkResult = $checkStmt->get_result();

  if ($checkResult && $checkResult->num_rows > 0) {
    send_error('Email already registered.', ['email' => 'This email is already in use.'], 422);
  }

  // Hash the password
  $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

  // Insert the new user
  $insertSql = 'INSERT INTO users (email, password, firstname, lastname) VALUES (?, ?, ?, ?)';
  $insertStmt = $connection->prepare($insertSql);

  if (!$insertStmt) {
    send_error('Database error.', ['detail' => $connection->error], 500);
  }

  $insertStmt->bind_param('ssss', $email, $hashedPassword, $firstname, $lastname);
  $insertStmt->execute();

  $lastId = $connection->insert_id;

  // Retrieve the created user (without password)
  $selectSql = 'SELECT id, email, firstname, lastname FROM users WHERE id = ?';
  $selectStmt = $connection->prepare($selectSql);

  if (!$selectStmt) {
    send_error('Database error.', ['detail' => $connection->error], 500);
  }

  $selectStmt->bind_param('i', $lastId);
  $selectStmt->execute();

  $result = $selectStmt->get_result();
  $user = $result ? $result->fetch_assoc() : null;

  send_success('User registered successfully.', $user, 201);
} catch (Throwable $exception) {
  send_error('Failed to register user.', ['detail' => $exception->getMessage()], 500);
}

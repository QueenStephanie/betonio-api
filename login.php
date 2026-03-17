<?php

declare(strict_types=1);

// Set CORS headers first, before anything else
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Max-Age: 3600');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit('');
}

require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/api_response.php';
require_once __DIR__ . '/jwt.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
  send_error('Method not allowed.', null, 405);
}

$input = read_json_input();
error_log("DEBUG LOGIN: input=" . json_encode($input));
$email = trim((string) ($input['email'] ?? ''));
$password = trim((string) ($input['password'] ?? ''));
error_log("DEBUG LOGIN: email=$email, password=$password");

$errors = [];
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $errors['email'] = 'Valid email is required.';
}
if ($password === '') {
  $errors['password'] = 'Password is required.';
}

if (!empty($errors)) {
  send_error('Login failed.', $errors, 422);
}

try {
  // Query the user by email
  $sql = 'SELECT id, email, password, firstname, lastname FROM users WHERE email = ?';
  $stmt = $connection->prepare($sql);

  if (!$stmt) {
    send_error('Database error.', ['detail' => $connection->error], 500);
  }

  $stmt->bind_param('s', $email);
  $stmt->execute();

  $result = $stmt->get_result();
  $user = $result ? $result->fetch_assoc() : null;

  if (!$user) {
    send_error('Invalid email or password.', null, 401);
  }

  // Verify password using bcrypt
  if (!password_verify($password, $user['password'])) {
    send_error('Invalid email or password.', null, 401);
  }

  // Remove password from response
  unset($user['password']);

  // Generate JWT token
  $token = jwt_encode([
    'id' => (int) $user['id'],
    'email' => $user['email'],
  ], get_jwt_secret());

  send_success('Login successful.', [
    'id' => $user['id'],
    'email' => $user['email'],
    'firstname' => $user['firstname'],
    'lastname' => $user['lastname'],
    'token' => $token
  ]);
} catch (Throwable $exception) {
  send_error('Login failed.', ['detail' => $exception->getMessage()], 500);
}

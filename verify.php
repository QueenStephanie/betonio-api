<?php

declare(strict_types=1);

require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/api_response.php';
require_once __DIR__ . '/jwt.php';

set_api_headers();
handle_preflight();

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
  send_error('Method not allowed.', null, 405);
}

try {
// Get the authorization header
  $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
  
  if (empty($authHeader)) {
    $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
  }
  
  if (empty($authHeader) || !preg_match('/Bearer\s+(.+)/', $authHeader, $matches)) {
    send_error('Authorization header missing or invalid.', null, 401);
  }

  $token = $matches[1];

  // Verify the token
  $decoded = jwt_decode($token, get_jwt_secret());
  
  if (!$decoded) {
    send_error('Invalid or expired token.', null, 401);
  }

  // Get fresh user data from database
  $sql = 'SELECT id, email, firstname, lastname FROM users WHERE id = ?';
  $stmt = $connection->prepare($sql);

  if (!$stmt) {
    send_error('Database error.', ['detail' => $connection->error], 500);
  }

  $userId = (int) $decoded['id'];
  $stmt->bind_param('i', $userId);
  $stmt->execute();

  $result = $stmt->get_result();
  $user = $result ? $result->fetch_assoc() : null;

  if (!$user) {
    send_error('User not found.', null, 401);
  }

  send_success('Token is valid.', $user);
} catch (Throwable $exception) {
  send_error('Token verification failed.', ['detail' => $exception->getMessage()], 500);
}

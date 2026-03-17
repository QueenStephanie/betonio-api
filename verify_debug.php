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
  
  error_log('verify_debug.php: Auth header: ' . $authHeader);

  if (empty($authHeader)) {
    send_error('Authorization header missing.', null, 401);
  }

  if (!preg_match('/Bearer\s+(.+)/', $authHeader, $matches)) {
    send_error('Authorization header format invalid.', null, 401);
  }

  $token = $matches[1];
  error_log('verify_debug.php: Token extracted: ' . substr($token, 0, 20) . '...');

  // Get the secret
  $secret = get_jwt_secret();
  error_log('verify_debug.php: JWT Secret: ' . $secret);

  // Verify the token
  $decoded = jwt_decode($token, $secret);
  
  error_log('verify_debug.php: Decoded payload: ' . json_encode($decoded));

  if (!$decoded) {
    send_error('Invalid or expired token.', [
      'auth_header' => substr($authHeader, 0, 50) . '...',
      'token_verified' => false,
      'jwt_secret_used' => $secret
    ], 401);
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

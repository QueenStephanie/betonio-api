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

require_once __DIR__ . '/api_response.php';
require_once __DIR__ . '/jwt.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
  send_error('Method not allowed.', null, 405);
}

try {
  // Get the authorization header
  $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

  if (empty($authHeader) || !preg_match('/Bearer\s+(.+)/', $authHeader, $matches)) {
    send_error('Authorization header missing or invalid.', null, 401);
  }

  $token = $matches[1];

  // Verify the token is valid before logout
  $decoded = jwt_decode($token, get_jwt_secret());

  if (!$decoded) {
    send_error('Invalid token.', null, 401);
  }

  // In a real app, you might invalidate the token in a blacklist table
  // For now, just return success and let the client remove the token

  send_success('Logged out successfully.');
} catch (Throwable $exception) {
  send_error('Logout failed.', ['detail' => $exception->getMessage()], 500);
}

<?php
// Minimal login endpoint for testing
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

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode([
    'status' => 'error',
    'message' => 'Method not allowed',
    'received_method' => $_SERVER['REQUEST_METHOD'],
    'allowed' => ['POST']
  ]);
  exit;
}

// Get input
$input = json_decode(file_get_contents('php://input'), true);
$email = ($input['email'] ?? '');
$password = ($input['password'] ?? '');

// Simple test response
if ($email === 'test@school.edu' && $password === 'password123') {
  http_response_code(200);
  echo json_encode([
    'status' => 'success',
    'message' => 'Login successful',
    'data' => [
      'id' => 1,
      'email' => 'test@school.edu',
      'firstname' => 'Test',
      'lastname' => 'User',
      'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwiZW1haWwiOiJ0ZXN0QHNjaG9vbC5lZHUiLCJpYXQiOjE3NDI4MTgwMDAsImV4cCI6MTc0MjkwNDQwMH0.test'
    ]
  ]);
} else {
  http_response_code(401);
  echo json_encode([
    'status' => 'error',
    'message' => 'Invalid credentials',
    'received' => ['email' => $email, 'password' => strlen($password) . ' chars']
  ]);
}
?>

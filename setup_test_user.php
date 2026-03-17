<?php

require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/api_response.php';

set_api_headers();

try {
  // Check if user already exists
  $checkSql = 'SELECT id FROM users WHERE email = ?';
  $checkStmt = $connection->prepare($checkSql);

  if (!$checkStmt) {
    send_error('Database error.', ['detail' => $connection->error], 500);
  }

  $email = 'test@school.edu';
  $checkStmt->bind_param('s', $email);
  $checkStmt->execute();
  $checkResult = $checkStmt->get_result();

  if ($checkResult && $checkResult->num_rows > 0) {
    send_success('Test user already exists.', [
      'email' => 'test@school.edu',
      'password' => 'password123',
      'message' => 'User already exists in database'
    ]);
  }

  // Hash the password
  $password = 'password123';
  $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

  // Insert the test user
  $insertSql = 'INSERT INTO users (email, password, firstname, lastname) VALUES (?, ?, ?, ?)';
  $insertStmt = $connection->prepare($insertSql);

  if (!$insertStmt) {
    send_error('Database error.', ['detail' => $connection->error], 500);
  }

  $firstname = 'Test';
  $lastname = 'User';
  $insertStmt->bind_param('ssss', $email, $hashedPassword, $firstname, $lastname);
  $insertStmt->execute();

  send_success('Test user created successfully!', [
    'email' => 'test@school.edu',
    'password' => 'password123',
    'firstname' => 'Test',
    'lastname' => 'User'
  ]);
} catch (Throwable $exception) {
  send_error('Failed to create test user.', ['detail' => $exception->getMessage()], 500);
}

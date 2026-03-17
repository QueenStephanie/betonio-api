<?php

require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/api_response.php';

set_api_headers();

try {
  // Check if users table exists
  $tableCheckSql = "SHOW TABLES LIKE 'users'";
  $tableResult = $connection->query($tableCheckSql);

  if (!$tableResult || $tableResult->num_rows === 0) {
    send_error('Users table does not exist.', [
      'message' => 'You need to create the users table first.',
      'sql' => 'CREATE TABLE users (id INT PRIMARY KEY AUTO_INCREMENT, email VARCHAR(255) UNIQUE NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(100), lastname VARCHAR(100), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)'
    ], 400);
  }

  // Check all users in the database
  $sql = 'SELECT id, email, firstname, lastname FROM users';
  $result = $connection->query($sql);

  if (!$result) {
    send_error('Query failed.', ['detail' => $connection->error], 500);
  }

  $users = [];
  while ($row = $result->fetch_assoc()) {
    $users[] = $row;
  }

  send_success('Users in database:', [
    'total_users' => count($users),
    'users' => $users
  ]);
} catch (Throwable $exception) {
  send_error('Debug check failed.', ['detail' => $exception->getMessage()], 500);
}

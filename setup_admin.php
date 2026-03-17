<?php

declare(strict_types=1);

require_once __DIR__ . '/db_connection.php';

$email = 'admin';
$password = 'admin123';
$firstname = 'Admin';
$lastname = 'User';
$contact = '0000000000';
$schoolIdNum = 'ADM001';

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Check if user exists
$checkSql = 'SELECT id FROM users WHERE email = ?';
$checkStmt = $connection->prepare($checkSql);
$checkStmt->bind_param('s', $email);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
  // Update existing user
  $updateSql = 'UPDATE users SET password = ?, firstname = ?, lastname = ?, contact = ?, school_idnum = ? WHERE email = ?';
  $updateStmt = $connection->prepare($updateSql);
  $updateStmt->bind_param('ssssss', $hashedPassword, $firstname, $lastname, $contact, $schoolIdNum, $email);
  $updateStmt->execute();
  echo json_encode([
    'status' => 'success',
    'message' => 'Admin user updated successfully',
    'email' => $email,
    'password' => $password
  ]);
} else {
  // Insert new user
  $insertSql = 'INSERT INTO users (firstname, lastname, contact, school_idnum, email, password) VALUES (?, ?, ?, ?, ?, ?)';
  $insertStmt = $connection->prepare($insertSql);
  $insertStmt->bind_param('ssssss', $firstname, $lastname, $contact, $schoolIdNum, $email, $hashedPassword);
  $insertStmt->execute();
  echo json_encode([
    'status' => 'success',
    'message' => 'Admin user created successfully',
    'email' => $email,
    'password' => $password
  ]);
}

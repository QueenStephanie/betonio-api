<?php

include_once 'cors.php';
include_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method. Only POST is allowed.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$id = isset($data['id']) ? intval($data['id']) : 0;

if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid student ID.']);
    exit();
}

$stmt = mysqli_prepare($connection, "DELETE FROM users WHERE id=?");
mysqli_stmt_bind_param($stmt, 'i', $id);
$result = mysqli_stmt_execute($stmt);

if ($result && mysqli_stmt_affected_rows($stmt) > 0) {
    echo json_encode(['status' => 'success', 'message' => 'Student deleted successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete student or student not found.']);
}

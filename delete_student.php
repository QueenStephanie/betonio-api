<?php

include_once 'cors.php';
include_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method. Only POST is allowed.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$id = isset($data['id']) ? intval($data['id']) : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid student ID.']);
    exit();
}

try {
    $stmt = $connection->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param('i', $id);
    $result = $stmt->execute();
    
    if ($result && $stmt->affected_rows > 0) {
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'Student deleted successfully.']);
    } else {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete student or student not found.']);
    }
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete student. ' . $e->getMessage()]);
}

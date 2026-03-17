<?php

include_once 'cors.php';
include_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method. Only POST is allowed.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$id           = isset($data['id'])           ? intval($data['id'])             : 0;
$firstname    = isset($data['firstname'])    ? trim($data['firstname'])        : '';
$lastname     = isset($data['lastname'])     ? trim($data['lastname'])         : '';
$contact      = isset($data['contact'])      ? trim($data['contact'])          : '';
$school_idnum = isset($data['school_idnum'])
    ? trim($data['school_idnum'])
    : (isset($data['school_id_number']) ? trim($data['school_id_number']) : '');
$email        = isset($data['email'])        ? trim($data['email'])            : '';

if ($id <= 0 || empty($firstname) || empty($lastname) || empty($contact) || empty($school_idnum) || empty($email)) {
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit();
}

// Check if student exists before updating
try {
    $check_stmt = $connection->prepare("SELECT id FROM users WHERE id=?");
    $check_stmt->bind_param('i', $id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Student not found.']);
        exit();
    }
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error checking student: ' . $e->getMessage()]);
    exit();
}

try {
    $stmt = $connection->prepare(
        "UPDATE users SET firstname=?, lastname=?, contact=?, school_idnum=?, email=? WHERE id=?"
    );
    $stmt->bind_param('sssssi', $firstname, $lastname, $contact, $school_idnum, $email, $id);
    $result = $stmt->execute();
    
    if ($result) {
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'Student updated successfully.']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to update student. ' . $connection->error]);
    }
} catch (mysqli_sql_exception $e) {
    $errorCode = $e->getCode();
    if ($errorCode === 1062) {
        http_response_code(409);
        echo json_encode(['status' => 'error', 'message' => 'A student with that School ID or Email already exists.']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to update student. ' . $e->getMessage()]);
    }
}

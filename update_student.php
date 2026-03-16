<?php

include_once 'cors.php';
include_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method. Only POST is allowed.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$id           = isset($data['id'])           ? intval($data['id'])             : 0;
$firstname    = isset($data['firstname'])    ? trim($data['firstname'])        : '';
$lastname     = isset($data['lastname'])     ? trim($data['lastname'])         : '';
$contact      = isset($data['contact'])      ? trim($data['contact'])          : '';
$school_idnum = isset($data['school_idnum']) ? trim($data['school_idnum'])     : '';
$email        = isset($data['email'])        ? trim($data['email'])            : '';

if ($id <= 0 || empty($firstname) || empty($lastname) || empty($contact) || empty($school_idnum) || empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit();
}

$stmt = $connection->prepare(
    "UPDATE users SET firstname=?, lastname=?, contact=?, school_idnum=?, email=? WHERE id=?"
);
$stmt->bind_param('sssssi', $firstname, $lastname, $contact, $school_idnum, $email, $id);
$result = $stmt->execute();

if ($result) {
    echo json_encode(['status' => 'success', 'message' => 'Student updated successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update student. ' . $connection->error]);
}

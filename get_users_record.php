<?php

include_once 'cors.php';
include_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(array('status' => 'error', 'message' => 'Invalid request method. Only GET is allowed.'));
    exit();
}

try {
    $stmt = $connection->prepare("SELECT id, firstname, lastname, contact, school_idnum AS school_idnum, email, created_at FROM users");
    $stmt->execute();
    $result = $stmt->get_result();

    $users = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }

    echo json_encode(array('status' => 'success', 'data' => $users));
} catch (mysqli_sql_exception $e) {
    echo json_encode(array('status' => 'error', 'message' => 'Error retrieving users: ' . $e->getMessage()));
}

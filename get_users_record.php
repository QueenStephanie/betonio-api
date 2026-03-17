<?php

include_once 'cors.php';
include_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(array('status' => 'error', 'message' => 'Invalid request method. Only GET is allowed.'));
    exit();
} else {
    try {
        $stmt = $connection->prepare("SELECT id, firstname, lastname, contact, school_id_number, email, created_at FROM users");
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $users = array();
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            echo json_encode(array('status' => 'success', 'data' => $users));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'No users found.'));
        }
    } catch (mysqli_sql_exception $e) {
        echo json_encode(array('status' => 'error', 'message' => 'Error retrieving users: ' . $e->getMessage()));
    }
}

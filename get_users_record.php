<?php

include_once 'cors.php';
include_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(array('status' => 'error', 'message' => 'Invalid request method. Only GET is allowed.'));
    exit();
} else {
    $sql = "SELECT * FROM users";
    $result = $connection->query($sql);
    if ($result->num_rows > 0) {
        $users = array();

        foreach ($result as $row) {
            $users[] = $row;
        }
        echo json_encode(array('status' => 'success', 'data' => $users));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'No users found.'));
    }
}

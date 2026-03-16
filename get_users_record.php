<?php

include_once 'cors.php';
include_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(array('status' => 'error', 'message' => 'Invalid request method. Only GET is allowed.'));
    exit();
} else {
    $sql = "SELECT * FROM users";
    $result = mysqli_query($connection, $sql);
    if (mysqli_num_rows($result) > 0) {
        $users = array();
       
        // while($row = mysqli_fetch_assoc($result)) {
        //     $users[] = $row;
        // }
        foreach ($result as $row) {
            $users[] = $row;
        }
        echo json_encode(array('status' => 'success', 'data' => $users));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'No users found.'));
    }
}

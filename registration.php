<?php 

    include_once 'cors.php';
    include_once 'db_connection.php';

    if($_SERVER['REQUEST_METHOD'] === 'POST') {

        $data = json_decode(file_get_contents("php://input"), true);

        $firstname    = isset($data['firstname'])    ? trim($data['firstname'])    : '';
        $lastname     = isset($data['lastname'])     ? trim($data['lastname'])     : '';
        $contact      = isset($data['contact'])      ? trim($data['contact'])      : '';
        $school_idnum = isset($data['school_idnum']) ? trim($data['school_idnum']) : '';
        $email        = isset($data['email'])        ? trim($data['email'])        : '';

        if (empty($firstname) || empty($lastname) || empty($contact) || empty($school_idnum) || empty($email)) {
            http_response_code(400);
            echo json_encode(array('status' => 'error', 'message' => 'All fields are required.', 'data' => null));
            exit();
        }

        try {
            $stmt = $connection->prepare(
                "INSERT INTO users (firstname, lastname, contact, school_idnum, email) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param('sssss', $firstname, $lastname, $contact, $school_idnum, $email);
            $stmt->execute();
            
            $student_id = $connection->insert_id;

            $response = array(
                'status'  => 'success',
                'message' => 'Student registered successfully.',
                'data'    => array(
                    'id'           => $student_id,
                    'firstname'    => $firstname,
                    'lastname'     => $lastname,
                    'contact'      => $contact,
                    'school_idnum' => $school_idnum,
                    'email'        => $email
                )
            );
        } catch (mysqli_sql_exception $e) {
            $errorCode = $e->getCode();
            if ($errorCode === 1062) {
                // Duplicate entry for unique key (school_idnum or email)
                http_response_code(409);
                $message = 'A student with that School ID or Email already exists.';
            } else {
                http_response_code(500);
                $message = 'Failed to register student. ' . $e->getMessage();
            }
            $response = array(
                'status'  => 'error',
                'message' => $message,
                'data'    => null
            );
        }

        echo json_encode($response);


    } 

?>

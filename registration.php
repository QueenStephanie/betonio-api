<?php 

    include_once 'cors.php';
    include_once 'db_connection.php';

    if($_SERVER['REQUEST_METHOD'] === 'POST') {

        $rawInput = file_get_contents("php://input");
        $jsonData = json_decode($rawInput, true);
        $data = is_array($jsonData) ? $jsonData : $_POST;

        if (isset($data['data']) && is_array($data['data'])) {
            $data = $data['data'];
        }

        $firstname    = isset($data['firstname']) ? trim($data['firstname']) : (isset($data['first_name']) ? trim($data['first_name']) : '');
        $lastname     = isset($data['lastname']) ? trim($data['lastname']) : (isset($data['last_name']) ? trim($data['last_name']) : '');
        $contact      = isset($data['contact']) ? trim($data['contact']) : (isset($data['phone']) ? trim($data['phone']) : '');
        $school_idnum = isset($data['school_idnum']) ? trim($data['school_idnum']) : (isset($data['schoolIdNum']) ? trim($data['schoolIdNum']) : (isset($data['school_id']) ? trim($data['school_id']) : ''));
        $email        = isset($data['email']) ? trim($data['email']) : '';

        if (empty($firstname) || empty($lastname) || empty($contact) || empty($school_idnum) || empty($email)) {
            echo json_encode(array('status' => 'error', 'message' => 'All fields are required.', 'data' => null));
            exit();
        }

        try {
            $stmt = $connection->prepare(
                "INSERT INTO users (firstname, lastname, contact, school_idnum, email) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param('sssss', $firstname, $lastname, $contact, $school_idnum, $email);
            $stmt->execute();

            $response = array(
                'status'  => 'success',
                'message' => 'Student registered successfully.',
                'data'    => array(
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
                $message = 'A student with that School ID or Email already exists.';
            } else {
                $message = 'Failed to register student. ' . $e->getMessage();
            }
            $response = array(
                'status'  => 'error',
                'message' => $message,
                'data'    => null
            );
        }

        echo json_encode($response);


    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Invalid request method. Only POST is allowed.', 'data' => null));
    }

?>

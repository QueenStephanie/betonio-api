<?php 
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'ipt_db');
    define('DB_PORT', 3306);

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);
    } catch (mysqli_sql_exception $e) {
        header('Content-Type: application/json');
        echo json_encode(array(
            'status' => 'error',
            'message' => 'Database connection failed: ' . $e->getMessage()
        ));
        exit();
    }
?>
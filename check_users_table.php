<?php

declare(strict_types=1);

require_once __DIR__ . '/db_connection.php';

$result = $connection->query("DESCRIBE users");
$columns = [];

while ($row = $result->fetch_assoc()) {
  $columns[] = $row;
}

echo json_encode($columns, JSON_PRETTY_PRINT);

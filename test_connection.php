<?php

echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Connection Test</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        .success { color: green; padding: 10px; border: 1px solid green; border-radius: 5px; margin: 10px 0; }
        .error { color: red; padding: 10px; border: 1px solid red; border-radius: 5px; margin: 10px 0; }
        .info { color: blue; padding: 10px; border: 1px solid blue; border-radius: 5px; margin: 10px 0; }
        code { background: #f4f4f4; padding: 5px 10px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔍 Database Connection Test</h1>";

// Test 1: Check if db_connection.php exists and loads
echo "<h2>Test 1: Loading Database Configuration</h2>";
if (file_exists(__DIR__ . '/db_connection.php')) {
  echo "<div class='success'>✅ db_connection.php found</div>";

  // Show configuration
  echo "<div class='info'>
        <strong>Configuration Details:</strong><br>
        Host: <code>localhost</code><br>
        Port: <code>3307</code><br>
        Database: <code>ipt_db</code><br>
        User: <code>root</code>
    </div>";
} else {
  echo "<div class='error'>❌ db_connection.php NOT found</div>";
}

// Test 2: Try to connect to database
echo "<h2>Test 2: Connecting to Database</h2>";
try {
  require_once __DIR__ . '/db_connection.php';

  if ($connection && !($connection instanceof mysqli)) {
    echo "<div class='error'>❌ Connection failed: Not a valid MySQLi object</div>";
  } else {
    echo "<div class='success'>✅ Connected to database successfully!</div>";

    // Show database info
    echo "<div class='info'>
            <strong>Database Information:</strong><br>
            Server: " . $connection->server_info . "<br>
            Database: " . $connection->query("SELECT DATABASE()")->fetch_row()[0] . "<br>
            Charset: " . $connection->get_charset()->charset . "
        </div>";
  }
} catch (mysqli_sql_exception $e) {
  echo "<div class='error'>❌ Connection Error:<br>" . htmlspecialchars($e->getMessage()) . "</div>";
  echo "<div class='info'>
        <strong>Troubleshooting:</strong><br>
        1. Make sure MySQL is running in XAMPP<br>
        2. Check if port 3307 is correct (might be 3306)<br>
        3. Verify database 'ipt_db' exists<br>
        4. Check db_connection.php configuration
    </div>";
} catch (Exception $e) {
  echo "<div class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Test 3: Check if users table exists
echo "<h2>Test 3: Checking Users Table</h2>";
try {
  if ($connection) {
    $tableCheck = $connection->query("SHOW TABLES LIKE 'users'");

    if ($tableCheck && $tableCheck->num_rows > 0) {
      echo "<div class='success'>✅ Users table exists</div>";

      // Show table structure
      $columns = $connection->query("DESCRIBE users");
      echo "<div class='info'><strong>Users Table Structure:</strong><br><pre>";
      echo "Column Name | Type | Null | Key\n";
      echo str_repeat("-", 50) . "\n";
      while ($col = $columns->fetch_assoc()) {
        printf(
          "%-20s | %-20s | %-4s | %s\n",
          $col['Field'],
          $col['Type'],
          $col['Null'],
          $col['Key']
        );
      }
      echo "</pre></div>";
    } else {
      echo "<div class='error'>❌ Users table does NOT exist</div>";
      echo "<div class='info'>
                <strong>Next Step:</strong><br>
                Go to Task 2 to create the users table
            </div>";
    }
  }
} catch (Exception $e) {
  echo "<div class='error'>❌ Error checking table: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "</body></html>";

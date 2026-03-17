<?php

echo "<!DOCTYPE html>
<html>
<head>
    <title>Automated Setup - Betonio API</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 40px;
        }
        h1 { 
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .step {
            margin: 30px 0;
            padding: 20px;
            border-left: 4px solid #667eea;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .step h2 {
            color: #333;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .success { 
            color: #28a745;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .error { 
            color: #dc3545;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .info { 
            color: #0c5460;
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .code {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
            margin: 10px 0;
        }
        .button {
            background: #667eea;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
            width: 100%;
        }
        .button:hover {
            background: #764ba2;
        }
        .button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .summary {
            background: #e7f3ff;
            border: 2px solid #667eea;
            padding: 20px;
            border-radius: 5px;
            margin: 30px 0;
        }
        .summary h3 {
            color: #667eea;
            margin-bottom: 15px;
        }
        .summary-item {
            margin: 10px 0;
            padding: 10px;
            background: white;
            border-radius: 3px;
        }
        .tick { color: #28a745; font-weight: bold; }
        .cross { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <div class='container'>";

$errors = [];
$successes = [];

// Step 1: Verify Connection
echo "<h1>⚙️ Automated Database Setup</h1>";
echo "<p class='subtitle'>Setting up authentication system for Betonio API</p>";

echo "<div class='step'>";
echo "<h2>Step 1: Verifying Database Connection</h2>";

try {
  require_once __DIR__ . '/db_connection.php';

  if (!($connection instanceof mysqli)) {
    $errors[] = "Database connection failed";
    echo "<div class='error'><span class='cross'>✗</span> Database connection failed</div>";
  } else {
    $successes[] = "Database connected";
    echo "<div class='success'><span class='tick'>✓</span> Database connected successfully</div>";
  }
} catch (Exception $e) {
  $errors[] = $e->getMessage();
  echo "<div class='error'><span class='cross'>✗</span> Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "</div>";

// Step 2: Create Users Table
echo "<div class='step'>";
echo "<h2>Step 2: Creating Users Table</h2>";

if (!isset($connection)) {
  $errors[] = "Cannot proceed - no database connection";
  echo "<div class='error'><span class='cross'>✗</span> Skipped - no database connection</div>";
} else {
  try {
    // Check if table exists
    $tableExists = $connection->query("SHOW TABLES LIKE 'users'");

    if ($tableExists && $tableExists->num_rows > 0) {
      $successes[] = "Users table already exists";
      echo "<div class='success'><span class='tick'>✓</span> Users table already exists</div>";
    } else {
      // Create table
      $createTableSql = "CREATE TABLE users (
                id INT PRIMARY KEY AUTO_INCREMENT,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                firstname VARCHAR(100),
                lastname VARCHAR(100),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";

      if ($connection->query($createTableSql)) {
        $successes[] = "Users table created";
        echo "<div class='success'><span class='tick'>✓</span> Users table created successfully</div>";
      } else {
        $errors[] = "Failed to create users table: " . $connection->error;
        echo "<div class='error'><span class='cross'>✗</span> Failed to create table: " . htmlspecialchars($connection->error) . "</div>";
      }
    }
  } catch (Exception $e) {
    $errors[] = $e->getMessage();
    echo "<div class='error'><span class='cross'>✗</span> Error: " . htmlspecialchars($e->getMessage()) . "</div>";
  }
}

echo "</div>";

// Step 3: Create Test User
echo "<div class='step'>";
echo "<h2>Step 3: Creating Test User</h2>";

if (!isset($connection)) {
  $errors[] = "Cannot proceed - no database connection";
  echo "<div class='error'><span class='cross'>✗</span> Skipped - no database connection</div>";
} else {
  try {
    $testEmail = 'test@school.edu';
    $testPassword = 'password123';

    // Check if user exists
    $checkStmt = $connection->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->bind_param('s', $testEmail);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult && $checkResult->num_rows > 0) {
      $successes[] = "Test user already exists";
      echo "<div class='success'><span class='tick'>✓</span> Test user already exists (test@school.edu)</div>";
    } else {
      // Hash password
      $hashedPassword = password_hash($testPassword, PASSWORD_BCRYPT);

      // Insert user
      $insertStmt = $connection->prepare("INSERT INTO users (email, password, firstname, lastname) VALUES (?, ?, ?, ?)");

      if (!$insertStmt) {
        throw new Exception($connection->error);
      }

      $firstname = 'Test';
      $lastname = 'User';
      $insertStmt->bind_param('ssss', $testEmail, $hashedPassword, $firstname, $lastname);

      if ($insertStmt->execute()) {
        $successes[] = "Test user created";
        echo "<div class='success'><span class='tick'>✓</span> Test user created successfully</div>";
        echo "<div class='info'>Email: <strong>test@school.edu</strong><br>Password: <strong>password123</strong></div>";
      } else {
        throw new Exception($insertStmt->error);
      }
    }
  } catch (Exception $e) {
    $errors[] = $e->getMessage();
    echo "<div class='error'><span class='cross'>✗</span> Error: " . htmlspecialchars($e->getMessage()) . "</div>";
  }
}

echo "</div>";

// Step 4: Verify Everything
echo "<div class='step'>";
echo "<h2>Step 4: Verifying Setup</h2>";

if (!isset($connection)) {
  echo "<div class='error'><span class='cross'>✗</span> Skipped - no database connection</div>";
} else {
  try {
    // Check users table
    $tableCheck = $connection->query("SHOW TABLES LIKE 'users'");
    $tableExists = $tableCheck && $tableCheck->num_rows > 0;

    // Check test user
    $userCheck = $connection->query("SELECT COUNT(*) as count FROM users WHERE email = 'test@school.edu'");
    $userResult = $userCheck->fetch_assoc();
    $userExists = $userResult['count'] > 0;

    if ($tableExists && $userExists) {
      $successes[] = "Setup verification passed";
      echo "<div class='success'><span class='tick'>✓</span> All systems ready!</div>";
    } else {
      if (!$tableExists) {
        $errors[] = "Users table missing";
        echo "<div class='error'><span class='cross'>✗</span> Users table missing</div>";
      }
      if (!$userExists) {
        $errors[] = "Test user missing";
        echo "<div class='error'><span class='cross'>✗</span> Test user missing</div>";
      }
    }
  } catch (Exception $e) {
    $errors[] = $e->getMessage();
    echo "<div class='error'><span class='cross'>✗</span> Verification error: " . htmlspecialchars($e->getMessage()) . "</div>";
  }
}

echo "</div>";

// Summary
echo "<div class='summary'>";
echo "<h3>📊 Setup Summary</h3>";

echo "<div class='summary-item'>";
echo "<strong>Success Count:</strong> <span class='tick'>" . count($successes) . "</span>";
echo "</div>";

if (!empty($errors)) {
  echo "<div class='summary-item'>";
  echo "<strong>Errors:</strong> <span class='cross'>" . count($errors) . "</span>";
  echo "<ul style='margin-left: 20px; margin-top: 10px;'>";
  foreach ($errors as $error) {
    echo "<li>" . htmlspecialchars($error) . "</li>";
  }
  echo "</ul>";
  echo "</div>";
}

if (empty($errors) && count($successes) >= 4) {
  echo "<div style='margin-top: 20px; padding: 15px; background: #d4edda; border-radius: 5px; text-align: center;'>";
  echo "<strong style='color: #28a745; font-size: 18px;'>✓ Setup Complete!</strong><br>";
  echo "<p style='margin-top: 10px; color: #666;'>You can now proceed to test the login system.</p>";
  echo "</div>";

  echo "<div style='margin-top: 20px;'>";
  echo "<h4>Next Steps:</h4>";
  echo "<ol style='margin-left: 20px;'>";
  echo "<li>Go to your React app at <code style='background: #f4f4f4; padding: 2px 5px;'>http://localhost:5173</code></li>";
  echo "<li>Login with:<br><strong>Email:</strong> test@school.edu<br><strong>Password:</strong> password123</li>";
  echo "<li>You should be redirected to the dashboard</li>";
  echo "</ol>";
  echo "</div>";
}

echo "</div>";

echo "    </div>
</body>
</html>";

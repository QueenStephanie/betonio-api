<?php

echo "<!DOCTYPE html>
<html>
<head>
    <title>API Status Check</title>
    <style>
        body { font-family: Arial; margin: 40px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .test { padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid #ddd; }
        .success { background: #d4edda; border-left-color: #28a745; color: #155724; }
        .error { background: #f8d7da; border-left-color: #dc3545; color: #721c24; }
        .info { background: #d1ecf1; border-left-color: #0c5460; color: #0c5460; }
        code { background: #f4f4f4; padding: 5px 10px; border-radius: 3px; font-family: monospace; }
        .endpoint { margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 5px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px 0; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 API Status Check</h1>";

// Check 1: Database Connection
echo "<div class='test info'>";
echo "<h2>1. Database Connection</h2>";
try {
    require_once __DIR__ . '/db_connection.php';
    if ($connection instanceof mysqli) {
        echo "<div class='success'>✓ Connected to: <code>" . $connection->query("SELECT DATABASE()")->fetch_row()[0] . "</code></div>";
    } else {
        echo "<div class='error'>✗ Connection not valid</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>✗ " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Check 2: API Response Helper
echo "<div class='test info'>";
echo "<h2>2. API Response Helper</h2>";
if (file_exists(__DIR__ . '/api_response.php')) {
    echo "<div class='success'>✓ api_response.php loaded</div>";
} else {
    echo "<div class='error'>✗ api_response.php not found</div>";
}

// Check 3: JWT Helper
echo "<div class='test info'>";
echo "<h2>3. JWT Helper</h2>";
if (file_exists(__DIR__ . '/jwt.php')) {
    require_once __DIR__ . '/jwt.php';
    echo "<div class='success'>✓ jwt.php loaded</div>";
} else {
    echo "<div class='error'>✗ jwt.php not found</div>";
}

// Check 4: Test Users Table
echo "<div class='test info'>";
echo "<h2>4. Users Table</h2>";
try {
    $result = $connection->query("SELECT COUNT(*) as count FROM users");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<div class='success'>✓ Users table exists with <code>" . $row['count'] . "</code> users</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>✗ " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Check 5: API Endpoints
echo "<div class='test info'>";
echo "<h2>5. API Endpoints Available</h2>";

$endpoints = [
    'login.php' => 'POST - User login',
    'logout.php' => 'POST - User logout',
    'verify.php' => 'GET - Verify token',
    'register_user.php' => 'POST - Register user'
];

foreach ($endpoints as $file => $desc) {
    $exists = file_exists(__DIR__ . '/' . $file);
    $class = $exists ? 'success' : 'error';
    $icon = $exists ? '✓' : '✗';
    echo \"<div class='$class'>\n  $icon <code>$file</code> - $desc\n</div>\n\";
}

// Check 6: CORS Headers
echo \"<div class='test info'>
<h2>6. CORS Configuration</h2>
<p>Check if CORS headers are being sent correctly:</p>
<button onclick='testCORS()'>Test CORS</button>
<pre id='corsResult' style='background: white; padding: 10px; border-radius: 3px; display: none;'></pre>
</div>\";

// Check 7: Test Login Endpoint
echo \"<div class='test info'>
<h2>7. Test Login Endpoint</h2>
<button onclick='testLogin()'>Test Login</button>
<pre id='loginResult' style='background: white; padding: 10px; border-radius: 3px; display: none;'></pre>
</div>\";

// Check 8: Test Verify Endpoint
echo \"<div class='test info'>
<h2>8. Test Verify Endpoint</h2>
<p>Note: You need a valid token first</p>
<input type='text' id='tokenInput' placeholder='Paste JWT token here' style='width: 100%; padding: 8px; margin: 10px 0; border: 1px solid #ccc; border-radius: 3px;'>
<button onclick='testVerify()'>Test Verify</button>
<pre id='verifyResult' style='background: white; padding: 10px; border-radius: 3px; display: none;'></pre>
</div>\";

echo \"
    </div>

    <script>
    function testCORS() {
        const result = document.getElementById('corsResult');
        result.style.display = 'block';
        result.innerHTML = 'Testing...';
        
        fetch('http://localhost/betonio-api/api_response.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            let output = '';
            response.headers.forEach((value, name) => {
                if (name.includes('access-control') || name.includes('content-type')) {
                    output += name + ': ' + value + '\\n';
                }
            });
            result.innerHTML = output || 'CORS headers present but minimal details';
        })
        .catch(err => {
            result.innerHTML = 'Error: ' + err.message;
        });
    }

    function testLogin() {
        const result = document.getElementById('loginResult');
        result.style.display = 'block';
        result.innerHTML = 'Testing login endpoint...';

        fetch('http://localhost/betonio-api/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: 'test@school.edu',
                password: 'password123'
            })
        })
        .then(response => response.json())
        .then(data => {
            result.innerHTML = JSON.stringify(data, null, 2);
            if (data.data && data.data.token) {
                result.innerHTML += '\\n\\n✓ Login successful! Token received.';
                document.getElementById('tokenInput').value = data.data.token;
            }
        })
        .catch(err => {
            result.innerHTML = 'Error: ' + err.message;
        });
    }

    function testVerify() {
        const token = document.getElementById('tokenInput').value;
        if (!token) {
            alert('Please paste a token first');
            return;
        }

        const result = document.getElementById('verifyResult');
        result.style.display = 'block';
        result.innerHTML = 'Verifying token...';

        fetch('http://localhost/betonio-api/verify.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        })
        .then(response => response.json())
        .then(data => {
            result.innerHTML = JSON.stringify(data, null, 2);
        })
        .catch(err => {
            result.innerHTML = 'Error: ' + err.message;
        });
    }
    </script>
</body>
</html>";
?>

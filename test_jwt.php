<?php

declare(strict_types=1);

require_once __DIR__ . '/api_response.php';
require_once __DIR__ . '/jwt.php';

set_api_headers();

// Test JWT encode/decode
$secret = get_jwt_secret();

echo "<!DOCTYPE html>
<html>
<head>
    <title>JWT Test</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        .test { padding: 20px; margin: 20px 0; border: 1px solid #ccc; border-radius: 5px; }
        .success { background: #d4edda; border-color: #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        code { background: #f4f4f4; padding: 10px; display: block; margin: 10px 0; word-break: break-all; }
    </style>
</head>
<body>
    <h1>JWT Token Test</h1>";

// Test 1: Create a token
echo "<div class='test success'>";
echo "<h2>Test 1: Creating Token</h2>";

$testPayload = [
    'id' => 1,
    'email' => 'test@school.edu'
];

$token = jwt_encode($testPayload, $secret);
echo "<p><strong>Generated Token:</strong></p>";
echo "<code>$token</code>";
echo "</div>";

// Test 2: Decode the token
echo "<div class='test success'>";
echo "<h2>Test 2: Decoding Token</h2>";

$decoded = jwt_decode($token, $secret);
echo "<p><strong>Decoded Payload:</strong></p>";
echo "<code>" . json_encode($decoded, JSON_PRETTY_PRINT) . "</code>";

if ($decoded && $decoded['id'] === 1 && $decoded['email'] === 'test@school.edu') {
    echo "<p style='color: green;'><strong>✓ Token encode/decode working correctly!</strong></p>";
} else {
    echo "<p style='color: red;'><strong>✗ Token decode failed!</strong></p>";
}
echo "</div>";

// Test 3: Try with wrong secret
echo "<div class='test error'>";
echo "<h2>Test 3: Decoding with Wrong Secret</h2>";

$wrongSecret = 'wrong-secret';
$decodedWrong = jwt_decode($token, $wrongSecret);
echo "<p><strong>Result:</strong> " . ($decodedWrong ? 'Decoded (WRONG!)' : 'Failed to decode (Correct!)') . "</p>";
echo "<p>This test verifies that using a different secret doesn't work.</p>";
echo "</div>";

// Test 4: Check the JWT secret being used
echo "<div class='test'>";
echo "<h2>Test 4: Current JWT Secret</h2>";
echo "<p><strong>Secret:</strong> <code>$secret</code></p>";
echo "<p>This is the secret used for token generation and validation.</p>";
echo "</div>";

echo "</body>
</html>";
?>

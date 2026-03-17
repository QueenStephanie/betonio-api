<?php

echo "<!DOCTYPE html>
<html>
<head>
    <title>Simple API Test</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 40px;
        }
        h1 { color: #333; margin-bottom: 30px; text-align: center; }
        .test-section {
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        h2 { color: #333; font-size: 16px; margin-bottom: 15px; }
        button {
            background: #667eea;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
        }
        button:hover { background: #764ba2; }
        button:disabled { background: #ccc; cursor: not-allowed; }
        .output {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            margin-top: 15px;
            min-height: 100px;
            max-height: 300px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-all;
        }
        .success { border-left-color: #28a745; background: #d4edda; }
        .error { border-left-color: #dc3545; background: #f8d7da; }
        .info { border-left-color: #0c5460; background: #d1ecf1; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🧪 Simple API Test</h1>

        <div class='test-section info'>
            <h2>1️⃣ Test CORS</h2>
            <p>Click to test if CORS headers are being sent</p>
            <button onclick='testEndpoint(\"cors\")'>Test CORS</button>
            <div class='output' id='corsOutput'></div>
        </div>

        <div class='test-section info'>
            <h2>2️⃣ Test Login API</h2>
            <p>Click to test if login endpoint works</p>
            <button onclick='testEndpoint(\"login\")'>Test Login</button>
            <div class='output' id='loginOutput'></div>
        </div>

        <div class='test-section info'>
            <h2>3️⃣ Test Verify API</h2>
            <p>First login above to get a token, then test verify</p>
            <button id='verifyBtn' onclick='testEndpoint(\"verify\")' disabled>Test Verify</button>
            <div class='output' id='verifyOutput'></div>
        </div>

        <div class='test-section info'>
            <h2>4️⃣ Current Token</h2>
            <div class='output' id='tokenOutput'>No token yet. Login first.</div>
        </div>
    </div>

    <script>
    let currentToken = null;

    function testEndpoint(type) {
        const corsOutput = document.getElementById('corsOutput');
        const loginOutput = document.getElementById('loginOutput');
        const verifyOutput = document.getElementById('verifyOutput');
        const tokenOutput = document.getElementById('tokenOutput');
        const verifyBtn = document.getElementById('verifyBtn');

        if (type === 'cors') {
            corsOutput.textContent = 'Testing CORS...';
            
            fetch('http://localhost/betonio-api/cors_test.php')
            .then(async response => {
                const contentType = response.headers.get('content-type');
                let data;
                
                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                } else {
                    data = await response.text();
                }
                
                corsOutput.textContent = '✓ CORS Success!\\n\\nStatus: ' + response.status + '\\n\\n' + JSON.stringify(data, null, 2);
            })
            .catch(error => {
                corsOutput.textContent = '✗ CORS Error: ' + error.message;
            });
            return;
        }

        if (type === 'login') {
            loginOutput.textContent = 'Testing login...';
            
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
            .then(async response => {
                const contentType = response.headers.get('content-type');
                let data;
                
                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                } else {
                    data = await response.text();
                }
                
                if (!response.ok) {
                    loginOutput.textContent = 'Status: ' + response.status + '\\n\\n' + JSON.stringify(data, null, 2);
                    return;
                }
                
                if (data.data && data.data.token) {
                    currentToken = data.data.token;
                    tokenOutput.textContent = 'Token: ' + currentToken.substring(0, 50) + '...\\n\\nUser: ' + JSON.stringify(data.data, null, 2);
                    verifyBtn.disabled = false;
                    loginOutput.textContent = '✓ Login Success!\\n\\n' + JSON.stringify(data, null, 2);
                } else {
                    loginOutput.textContent = 'Response received but no token:\\n\\n' + JSON.stringify(data, null, 2);
                }
            })
            .catch(error => {
                loginOutput.textContent = '✗ Error: ' + error.message + '\\n\\nPossible causes:\\n1. CORS issue\\n2. API endpoint not found\\n3. Network problem';
            });
        }
        
        if (type === 'verify') {
            if (!currentToken) {
                verifyOutput.textContent = 'No token available. Please login first.';
                return;
            }
            
            verifyOutput.textContent = 'Verifying token...';
            
            fetch('http://localhost/betonio-api/verify.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + currentToken
                }
            })
            .then(async response => {
                const contentType = response.headers.get('content-type');
                let data;
                
                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                } else {
                    data = await response.text();
                }
                
                if (!response.ok) {
                    verifyOutput.textContent = 'Status: ' + response.status + '\\n\\n' + JSON.stringify(data, null, 2);
                    return;
                }
                
                verifyOutput.textContent = '✓ Verify Success!\\n\\n' + JSON.stringify(data, null, 2);
            })
            .catch(error => {
                verifyOutput.textContent = '✗ Error: ' + error.message + '\\n\\nPossible causes:\\n1. CORS issue\\n2. API endpoint not found\\n3. Token invalid\\n4. Network problem';
            });
        }
    }
    </script>
</body>
</html>";

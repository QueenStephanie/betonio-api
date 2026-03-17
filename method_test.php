<?php

declare(strict_types=1);

// Set CORS headers first, before anything else
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Max-Age: 3600');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit('');
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';
$contentType = $_SERVER['CONTENT_TYPE'] ?? 'UNKNOWN';

$response = [
  'status' => 'success',
  'method' => $method,
  'content_type' => $contentType,
  'all_headers' => getallheaders(),
  'server_vars' => [
    'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? null,
    'CONTENT_TYPE' => $_SERVER['CONTENT_TYPE'] ?? null,
    'HTTP_CONTENT_TYPE' => $_SERVER['HTTP_CONTENT_TYPE'] ?? null,
  ]
];

if ($method === 'POST') {
  $response['body'] = file_get_contents('php://input');
}

http_response_code(200);
echo json_encode($response, JSON_PRETTY_PRINT);

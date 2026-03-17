<?php

declare(strict_types=1);

function set_api_headers(): void
{
  header('Content-Type: application/json; charset=utf-8');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
  header('Access-Control-Allow-Headers: Content-Type, Authorization');
  header('Access-Control-Expose-Headers: Content-Type, Authorization');
  header('Access-Control-Max-Age: 3600');
}

function handle_preflight(): void
{
  if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(204);
    exit;
  }
}

function read_json_input(): array
{
  $raw = file_get_contents('php://input');

  if ($raw === false || $raw === '') {
    return [];
  }

  $decoded = json_decode($raw, true);

  return is_array($decoded) ? $decoded : [];
}

function send_success(string $message, $data = null, int $statusCode = 200): void
{
  http_response_code($statusCode);

  $payload = [
    'status' => 'success',
    'message' => $message,
  ];

  if ($data !== null) {
    $payload['data'] = $data;
  }

  echo json_encode($payload);
  exit;
}

function send_error(string $message, $errors = null, int $statusCode = 400): void
{
  http_response_code($statusCode);

  $payload = [
    'status' => 'error',
    'message' => $message,
  ];

  if ($errors !== null) {
    $payload['errors'] = $errors;
  }

  echo json_encode($payload);
  exit;
}

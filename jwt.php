<?php

declare(strict_types=1);

function jwt_encode(array $payload, string $secret): string
{
  // Header
  $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
  $headerEncoded = rtrim(strtr(base64_encode($header), '+/', '-_'), '=');

  // Payload
  $payload['iat'] = time();
  $payload['exp'] = time() + (24 * 60 * 60); // 24 hours
  $payloadEncoded = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');

  // Signature
  $signature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", $secret, true);
  $signatureEncoded = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

  return "$headerEncoded.$payloadEncoded.$signatureEncoded";
}

function jwt_decode(string $token, string $secret): ?array
{
  $parts = explode('.', $token);

  if (count($parts) !== 3) {
    return null;
  }

  [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;

  // Verify signature
  $signature = hash_hmac(
    'sha256',
    "$headerEncoded.$payloadEncoded",
    $secret,
    true
  );
  $expectedSignature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

  if (!hash_equals($expectedSignature, $signatureEncoded)) {
    return null;
  }

  // Decode payload
  $payloadDecoded = json_decode(
    base64_decode(strtr($payloadEncoded, '-_', '+/')),
    true
  );

  if (!is_array($payloadDecoded)) {
    return null;
  }

  // Check expiration
  if (isset($payloadDecoded['exp']) && $payloadDecoded['exp'] < time()) {
    return null;
  }

  return $payloadDecoded;
}

function get_jwt_secret(): string
{
  return getenv('JWT_SECRET') ?: 'your-secret-key-change-this-in-production';
}

<?php

/**
 * Generate JWT token
 * @param array<string,mixed> $payload
 * @param string $secret
 * @return string
 */
function generateJWT(array $payload, string $secret): string {
    // Create JWT header
    $header = [
        'typ' => 'JWT',
        'alg' => 'HS256'
    ];

    // Encode header and payload
    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($header)));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));

    // Create signature
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    // Create JWT
    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}

/**
 * Verify JWT token and extract user data
 * @param string $token JWT token
 * @return array<string,mixed> Decoded payload
 * @throws Exception When token is invalid
 */
function verifyJWT(string $token): array {
    $jwt_secret = getenv('JWT_SECRET') ?: '6AC9AE9E7932287592B39D4772638';
    
    $tokenParts = explode('.', $token);
    if (count($tokenParts) != 3) {
        throw new Exception('Invalid token format');
    }

    [$base64UrlHeader, $base64UrlPayload, $base64UrlSignature] = $tokenParts;

    // Verify signature
    $signature = base64_decode(str_replace(['-', '_'], ['+', '/'], $base64UrlSignature));
    $expectedSignature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $jwt_secret, true);
    
    if (!hash_equals($signature, $expectedSignature)) {
        throw new Exception('Invalid token signature');
    }

    // Decode payload
    $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $base64UrlPayload)), true);
    
    // Check token expiration
    if (isset($payload['exp']) && $payload['exp'] < time()) {
        throw new Exception('Token has expired');
    }

    return $payload;
}

/**
 * Get authenticated user ID from JWT token in cookie
 * @return int User ID
 * @throws Exception When authentication fails
 */
function getAuthenticatedUserId(): int {
    $token = $_COOKIE['jwt_token'] ?? null;
    if (!$token) {
        throw new Exception('Authentication required');
    }

    try {
        $payload = verifyJWT($token);
        $userId = $payload['user_id'] ?? null;
        if (!$userId) {
            throw new Exception('Invalid user ID in token');
        }
        return $userId;
    } catch (Exception $e) {
        throw new Exception('Authentication failed: ' . $e->getMessage());
    }
} 
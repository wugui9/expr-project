<?php
require_once __DIR__ . '/../model/php/env_settings.php';
require_once __DIR__ . '/ApiRouter.php';
require_once __DIR__ . '/../schemas/user_schema.php';
require_once __DIR__ . '/../model/repository/UserRepository.php';

$router = new ApiRouter();

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
 * Handle user login request
 * @param array<string,mixed> $data Login credentials
 * @return array<string,mixed> User data with JWT token
 * @throws Exception When login fails
 */
function handleLogin(array $data): array
{
    $req = LoginRequest::fromArray($data);
    if (!$req->validate()) {
        throw new Exception('Invalid login data');
    }

    $database = new DBModel();
    $userRepository = new UserRepository($database->get_connection());
    
    // Find user by email
    $user = $userRepository->findByEmail($req->email);
    if (!$user) {
        throw new Exception('User not found');
    }

    // Verify password
    if (!$userRepository->verifyPassword($user, $req->password)) {
        throw new Exception('Invalid password');
    }

    // Generate JWT token
    $issuedAt = time();
    $expirationTime = $issuedAt + 60 * 60 * 24; // 24 hours
    $payload = [
        'iat' => $issuedAt,
        'exp' => $expirationTime,
        'user_id' => $user->id,
        'email' => $user->email,
        'role' => $user->role
    ];

    $jwt_secret = getenv('JWT_SECRET') ?: 'your-256-bit-secret';
    $token = generateJWT($payload, $jwt_secret);

    // Set JWT token in HTTP-only cookie
    $cookie_options = [
        'expires' => $expirationTime,
        'path' => '/',
        'domain' => '',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ];
    setcookie('jwt_token', $token, $cookie_options);

    // Return user data with token
    $response = LoginResponse::fromEntity($user, $token);
    return $response->toArray();
}

$router->register('POST', 'users/login', 'handleLogin');

$router->handleRequest();

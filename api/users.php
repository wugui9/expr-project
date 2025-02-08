<?php
require_once __DIR__ . '/../model/php/env_settings.php';
require_once __DIR__ . '/ApiRouter.php';
require_once __DIR__ . '/../schemas/user_schema.php';
require_once __DIR__ . '/../model/repository/UserRepository.php';
require_once __DIR__ . '/../utils/session.php';

$router = new ApiRouter();

/**
 * Handle user login request
 * @param array<string,mixed> $data Login credentials
 * @return array<string,mixed> User data
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

    $jwt_secret = getenv('JWT_SECRET') ?: '6AC9AE9E7932287592B39D4772638';
    $token = generateJWT($payload, $jwt_secret);

    // Set JWT token in HTTP-only cookie
    setcookie('jwt_token', $token, [
        'expires' => $expirationTime,
        'path' => '/',
        'domain' => '',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);

    // Return user data without token
    $response = LoginResponse::fromEntity($user, $token);
    return $response->toArray();
}

/**
 * Get current authenticated user information
 * @return array<string,mixed> User data
 * @throws Exception When not authenticated
 */
function getCurrentUser(): array
{
    try {
        $userId = getAuthenticatedUserId();
        
        $database = new DBModel();
        $userRepository = new UserRepository($database->get_connection());
        
        /** @var User|null $user */
        $user = $userRepository->findById($userId);
        if (!$user) {
            http_response_code(404);
            return [
                'error' => 'User not found'
            ];
        }

        // Convert User entity to array
        return [
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'phone' => $user->phone,
            'address' => $user->address ?? null
        ];
    } catch (Exception $e) {
        http_response_code(401);
        return [
            'error' => 'Not authenticated: ' . $e->getMessage()
        ];
    }
}

/**
 * Handle user logout request
 * @return array<string,mixed> Success message
 */
function handleLogout(): array
{
    // Clear JWT token cookie
    setcookie('jwt_token', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'domain' => '',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);

    return [
        'success' => true,
        'message' => 'Logged out successfully'
    ];
}

$router->register('POST', 'users/login', 'handleLogin');
$router->register('GET', 'users/current', 'getCurrentUser');
$router->register('POST', 'users/logout', 'handleLogout');

$router->handleRequest();

<?php
require_once __DIR__ . '/../model/php/env_settings.php';
require_once __DIR__ . '/ApiRouter.php';
require_once __DIR__ . '/../schemas/order_schema.php';
require_once __DIR__ . '/../model/repository/OrderRepository.php';
require_once __DIR__ . '/../utils/session.php';

$router = new ApiRouter();

/**
 * Verify JWT token and extract user data
 * @param string $token JWT token
 * @return array<string,mixed> Decoded payload
 * @throws Exception When token is invalid
 */
function verifyJWT(string $token): array {
    $jwt_secret = getenv('JWT_SECRET') ?: 'your-256-6AC9AE9E7932287592B39D4772638-secret';
    
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
 * Handle create order request
 * @param array<string,mixed> $data Order data
 * @return array<string,mixed> Created order data
 * @throws Exception When creation fails
 */
function handleCreateOrder(array $data): array {
    // Get authenticated user ID
    $userId = getAuthenticatedUserId();
    
    // Validate required fields
    $requiredFields = [
        'weight',
        'delivery_level',
        'paid_amount',
        'payment_method',
        'recipient_lastname',
        'recipient_firstname',
        'recipient_phone',
        'shipping_address',
        'delivery_address'
    ];
    
    foreach ($requiredFields as $field) {
        if (!isset($data[$field])) {
            throw new Exception("Missing required field: {$field}");
        }
    }

    // Generate unique order number
    $orderNumber = 'ORD' . date('YmdHis') . rand(1000, 9999);
    
    // Create Order object with authenticated user ID
    $order = new Order(
        $orderNumber,
        $userId,
        new DateTime(),
        $data['paid_amount'],
        strtoupper($data['payment_method']),
        $data['shipping_address'],
        $data['delivery_address'],
        $data['recipient_lastname'],
        $data['recipient_firstname'],
        $data['recipient_phone'],
        $data['weight'],
        $data['delivery_level'],
        null,
        $data['relay_point_id'] ?? null
    );

    // Save order to database
    $database = new DBModel();
    $orderRepository = new OrderRepository($database->get_connection());
    $savedOrder = $orderRepository->save($order);

    // Return order data
    return [
        'id' => $savedOrder->getId(),
        'order_number' => $savedOrder->getOrderNumber(),
        'order_time' => $savedOrder->getOrderTime()->format('Y-m-d H:i:s'),
        'paid_amount' => $savedOrder->getPaidAmount(),
        'payment_method' => $savedOrder->getPaymentMethod(),
        'delivery_address' => $savedOrder->getDeliveryAddress(),
        'recipient_lastname' => $savedOrder->getRecipientLastname(),
        'recipient_firstname' => $savedOrder->getRecipientFirstname(),
        'recipient_phone' => $savedOrder->getRecipientPhone(),
        'weight' => $savedOrder->getWeight(),
        'delivery_level' => $savedOrder->getDeliveryLevel(),
        'relay_point_id' => $savedOrder->getRelayPointId()
    ];
}

// Register routes
$router->register('POST', 'order/orders', 'handleCreateOrder');

$router->handleRequest();

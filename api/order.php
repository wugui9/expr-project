<?php
require_once __DIR__ . '/../model/php/env_settings.php';
require_once __DIR__ . '/ApiRouter.php';
require_once __DIR__ . '/../schemas/order_schema.php';
require_once __DIR__ . '/../model/repository/OrderRepository.php';
require_once __DIR__ . '/../utils/session.php';

$router = new ApiRouter();

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

/**
 * Handle get order by ID request
 * @param int $orderId Order ID
 * @return array<string,mixed> Order data
 * @throws Exception When order not found
 */
function handleGetOrder(int $orderId): array {
    // Get authenticated user ID for security check
    $userId = getAuthenticatedUserId();
    
    // Get order from database
    $database = new DBModel();
    $orderRepository = new OrderRepository($database->get_connection());
    $order = $orderRepository->findById($orderId);
    
    if (!$order) {
        throw new Exception("Order not found", 404);
    }
    
    // Security check - only allow access to own orders
    if ($order->getSenderId() !== $userId) {
        throw new Exception("Unauthorized access", 403);
    }

    // Return order data
    return [
        'id' => $order->getId(),
        'order_number' => $order->getOrderNumber(),
        'order_time' => $order->getOrderTime()->format('Y-m-d H:i:s'),
        'paid_amount' => $order->getPaidAmount(),
        'payment_method' => $order->getPaymentMethod(),
        'shipping_address' => $order->getShippingAddress(),
        'delivery_address' => $order->getDeliveryAddress(),
        'recipient_lastname' => $order->getRecipientLastname(),
        'recipient_firstname' => $order->getRecipientFirstname(),
        'recipient_phone' => $order->getRecipientPhone(),
        'weight' => $order->getWeight(),
        'delivery_level' => $order->getDeliveryLevel(),
        'relay_point_id' => $order->getRelayPointId()
    ];
}

// Register routes
$router->register('POST', 'order/orders', 'handleCreateOrder');
$router->register('GET', 'order', function() {
    $orderId = isset($_GET['id']) ? (int)$_GET['id'] : null;
    if (!$orderId) {
        throw new Exception("Order ID is required", 400);
    }
    return handleGetOrder($orderId);
});

$router->handleRequest();

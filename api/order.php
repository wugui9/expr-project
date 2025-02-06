<?php
require_once __DIR__ . '/../model/php/env_settings.php';
require_once __DIR__ . '/ApiRouter.php';
require_once __DIR__ . '/../schemas/order_schema.php';
require_once __DIR__ . '/../model/repository/OrderRepository.php';

$router = new ApiRouter();

/**
 * Handle create order request
 * @param array<string,mixed> $data Order data
 * @return array<string,mixed> Created order data
 * @throws Exception When creation fails
 */
function handleCreateOrder(array $data): array {
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
    
    // Create Order object
    $order = new Order(
        $orderNumber,
        1, // TODO: Get sender_id from authenticated user
        new DateTime(),
        $data['paid_amount'],
        strtoupper($data['payment_method']), // Convert to uppercase to match enum
        $data['shipping_address'],
        $data['delivery_address'],
        $data['recipient_lastname'],
        $data['recipient_firstname'],
        $data['recipient_phone'],
        $data['weight'],
        $data['delivery_level'],
        null, // deliverer_id will be assigned later
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

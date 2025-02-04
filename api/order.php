<?php

require_once __DIR__ . '/../model/repository/OrderRepository.php';
require_once __DIR__ . '/../schemas/order_schema.php';

class OrderApi {
    private OrderRepository $orderRepository;

    public function __construct(PDO $db) {
        $this->orderRepository = new OrderRepository($db);
    }

    public function createOrder($data) {
        global $create_order_schema;
        
        // Validate request data
        $validator = new JsonSchema\Validator();
        $validator->validate($data, $create_order_schema);
        
        if (!$validator->isValid()) {
            http_response_code(400);
            return ['errors' => $validator->getErrors()];
        }

        // Generate unique order number
        $orderNumber = 'ORD' . date('YmdHis') . rand(1000, 9999);

        // Create order object
        $order = new Order(
            $orderNumber,
            $data['sender_id'],
            new DateTime($data['order_time']),
            $data['paid_amount'],
            $data['payment_method'],
            $data['shipping_address'],
            $data['delivery_address'],
            $data['recipient_lastname'],
            $data['recipient_firstname'],
            $data['recipient_phone'],
            $data['weight'],
            $data['delivery_level'],
            $data['deliverer_id'] ?? null,
            $data['relay_point_id'] ?? null
        );

        // Save to database
        if ($this->orderRepository->create($order)) {
            http_response_code(201);
            return ['order_number' => $orderNumber];
        } else {
            http_response_code(500);
            return ['error' => 'Failed to create order'];
        }
    }

    public function getOrder($orderNumber) {
        $order = $this->orderRepository->findByOrderNumber($orderNumber);
        
        if (!$order) {
            http_response_code(404);
            return ['error' => 'Order not found'];
        }

        return [
            'id' => $order->getId(),
            'order_number' => $order->getOrderNumber(),
            'sender_id' => $order->getSenderId(),
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
            'deliverer_id' => $order->getDelivererId(),
            'relay_point_id' => $order->getRelayPointId()
        ];
    }

    public function getUserOrders($userId) {
        $orders = $this->orderRepository->findBySenderId($userId);
        return array_map(fn($order) => [
            'id' => $order->getId(),
            'order_number' => $order->getOrderNumber(),
            'sender_id' => $order->getSenderId(),
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
            'deliverer_id' => $order->getDelivererId(),
            'relay_point_id' => $order->getRelayPointId()
        ], $orders);
    }

    public function getDelivererOrders($delivererId) {
        $orders = $this->orderRepository->findByDelivererId($delivererId);
        return array_map(fn($order) => [
            'id' => $order->getId(),
            'order_number' => $order->getOrderNumber(),
            'sender_id' => $order->getSenderId(),
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
            'deliverer_id' => $order->getDelivererId(),
            'relay_point_id' => $order->getRelayPointId()
        ], $orders);
    }

    public function assignDeliverer($orderId, $data) {
        global $assign_deliverer_schema;
        
        // Validate request data
        $validator = new JsonSchema\Validator();
        $validator->validate($data, $assign_deliverer_schema);
        
        if (!$validator->isValid()) {
            http_response_code(400);
            return ['errors' => $validator->getErrors()];
        }

        if ($this->orderRepository->assignDeliverer($orderId, $data['deliverer_id'])) {
            return ['message' => 'Deliverer assigned successfully'];
        } else {
            http_response_code(500);
            return ['error' => 'Failed to assign deliverer'];
        }
    }

    public function assignRelayPoint($orderId, $data) {
        global $assign_relay_point_schema;
        
        // Validate request data
        $validator = new JsonSchema\Validator();
        $validator->validate($data, $assign_relay_point_schema);
        
        if (!$validator->isValid()) {
            http_response_code(400);
            return ['errors' => $validator->getErrors()];
        }

        if ($this->orderRepository->assignRelayPoint($orderId, $data['relay_point_id'])) {
            return ['message' => 'Relay point assigned successfully'];
        } else {
            http_response_code(500);
            return ['error' => 'Failed to assign relay point'];
        }
    }

    public function getAllOrders() {
        $orders = $this->orderRepository->findAll();
        return array_map(fn($order) => [
            'id' => $order->getId(),
            'order_number' => $order->getOrderNumber(),
            'sender_id' => $order->getSenderId(),
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
            'deliverer_id' => $order->getDelivererId(),
            'relay_point_id' => $order->getRelayPointId()
        ], $orders);
    }
} 
<?php

require_once __DIR__ . '/../entities/Order.php';

class OrderRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function create(Order $order): bool {
        $sql = "INSERT INTO `order` (
            order_number, sender_id, order_time, paid_amount, 
            payment_method, shipping_address, delivery_address,
            recipient_lastname, recipient_firstname, recipient_phone,
            weight, delivery_level, deliverer_id, relay_point_id
        ) VALUES (
            :order_number, :sender_id, :order_time, :paid_amount,
            :payment_method, :shipping_address, :delivery_address,
            :recipient_lastname, :recipient_firstname, :recipient_phone,
            :weight, :delivery_level, :deliverer_id, :relay_point_id
        )";

        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
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
        ]);
    }

    public function findById(int $id): ?Order {
        $sql = "SELECT * FROM `order` WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        }

        return $this->createOrderFromArray($result);
    }

    public function findByOrderNumber(string $orderNumber): ?Order {
        $sql = "SELECT * FROM `order` WHERE order_number = :order_number";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['order_number' => $orderNumber]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        }

        return $this->createOrderFromArray($result);
    }

    public function findBySenderId(int $senderId): array {
        $sql = "SELECT * FROM `order` WHERE sender_id = :sender_id ORDER BY order_time DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['sender_id' => $senderId]);
        
        return array_map([$this, 'createOrderFromArray'], $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function findByDelivererId(int $delivererId): array {
        $sql = "SELECT * FROM `order` WHERE deliverer_id = :deliverer_id ORDER BY order_time DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['deliverer_id' => $delivererId]);
        
        return array_map([$this, 'createOrderFromArray'], $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function assignDeliverer(int $id, ?int $delivererId): bool {
        $sql = "UPDATE `order` SET deliverer_id = :deliverer_id WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'deliverer_id' => $delivererId,
            'id' => $id
        ]);
    }

    public function assignRelayPoint(int $id, ?int $relayPointId): bool {
        $sql = "UPDATE `order` SET relay_point_id = :relay_point_id WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'relay_point_id' => $relayPointId,
            'id' => $id
        ]);
    }

    public function findAll(): array {
        $sql = "SELECT * FROM `order` ORDER BY order_time DESC";
        $stmt = $this->db->query($sql);
        return array_map([$this, 'createOrderFromArray'], $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    private function createOrderFromArray(array $data): Order {
        return new Order(
            $data['order_number'],
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
            $data['deliverer_id'],
            $data['relay_point_id']
        );
    }
} 
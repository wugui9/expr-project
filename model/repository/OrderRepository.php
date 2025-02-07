<?php

require_once __DIR__ . '/../php/DBModel.php';
require_once __DIR__ . '/../entities/Order.php';
require_once __DIR__ . '/BaseRepository.php';

/**
 * @extends BaseRepository<Order>
 */
class OrderRepository extends BaseRepository {
    public function __construct($connection) 
    {
        parent::__construct($connection, 'order');
    }

    /**
     * Convert database row to Order entity
     * @param array $row Database row
     * @return Order
     */
    protected function mapToEntity(array $row): Order {
        $order = new Order(
            $row['order_number'],
            $row['sender_id'],
            new DateTime($row['order_time']),
            $row['paid_amount'],
            $row['payment_method'],
            $row['shipping_address'],
            $row['delivery_address'],
            $row['recipient_lastname'],
            $row['recipient_firstname'],
            $row['recipient_phone'],
            $row['weight'],
            $row['delivery_level'],
            $row['deliverer_id'],
            $row['relay_point_id']
        );

        // Set the ID
        $reflectionClass = new ReflectionClass('Order');
        $idProperty = $reflectionClass->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($order, $row['id']);

        return $order;
    }

    /**
     * Save a new order
     * @param Order $order
     * @return Order|null
     */
    public function save(Order $order): ?Order {
        $data = [
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

        if ($this->create($data)) {
            return $this->findByOrderNumber($order->getOrderNumber());
        }

        return null;
    }

    /**
     * Find order by order number
     * @param string $orderNumber
     * @return Order|null
     */
    public function findByOrderNumber(string $orderNumber): ?Order {
        $results = $this->findBy(['order_number' => $orderNumber]);
        return !empty($results) ? $results[0] : null;
    }

    /**
     * Find orders by sender ID
     * @param int $senderId
     * @return Order[]
     */
    public function findBySenderId(int $senderId): array {
        return $this->findBy(['sender_id' => $senderId]);
    }

    /**
     * Find orders by deliverer ID
     * @param int $delivererId
     * @return Order[]
     */
    public function findByDelivererId(int $delivererId): array {
        return $this->findBy(['deliverer_id' => $delivererId]);
    }

    /**
     * Assign deliverer to order
     * @param int $id
     * @param int|null $delivererId
     * @return bool
     */
    public function assignDeliverer(int $id, ?int $delivererId): bool {
        return $this->update($id, ['deliverer_id' => $delivererId]);
    }

    /**
     * Assign relay point to order
     * @param int $id
     * @param int|null $relayPointId
     * @return bool
     */
    public function assignRelayPoint(int $id, ?int $relayPointId): bool {
        return $this->update($id, ['relay_point_id' => $relayPointId]);
    }
} 
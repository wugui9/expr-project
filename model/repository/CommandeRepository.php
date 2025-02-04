<?php

require_once __DIR__ . '/../entities/Commande.php';

class CommandeRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function create(Commande $commande): bool {
        $sql = "INSERT INTO commande (
            order_number, sender_number, order_time, paid_amount, 
            payment_method, order_status, shipping_address, delivery_address,
            recipient_lastname, recipient_firstname, recipient_phone,
            weight, package_type, delivery_level, deliverer_number, relay_point_number
        ) VALUES (
            :order_number, :sender_number, :order_time, :paid_amount,
            :payment_method, :order_status, :shipping_address, :delivery_address,
            :recipient_lastname, :recipient_firstname, :recipient_phone,
            :weight, :package_type, :delivery_level, :deliverer_number, :relay_point_number
        )";

        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            'order_number' => $commande->getOrderNumber(),
            'sender_number' => $commande->getSenderNumber(),
            'order_time' => $commande->getOrderTime()->format('Y-m-d H:i:s'),
            'paid_amount' => $commande->getPaidAmount(),
            'payment_method' => $commande->getPaymentMethod(),
            'order_status' => $commande->getOrderStatus(),
            'shipping_address' => $commande->getShippingAddress(),
            'delivery_address' => $commande->getDeliveryAddress(),
            'recipient_lastname' => $commande->getRecipientLastname(),
            'recipient_firstname' => $commande->getRecipientFirstname(),
            'recipient_phone' => $commande->getRecipientPhone(),
            'weight' => $commande->getWeight(),
            'package_type' => $commande->getPackageType(),
            'delivery_level' => $commande->getDeliveryLevel(),
            'deliverer_number' => $commande->getDelivererNumber(),
            'relay_point_number' => $commande->getRelayPointNumber()
        ]);
    }

    public function findById(int $id): ?Commande {
        $sql = "SELECT * FROM commande WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        }

        return new Commande(
            $result['order_number'],
            $result['sender_number'],
            new DateTime($result['order_time']),
            $result['paid_amount'],
            $result['payment_method'],
            $result['order_status'],
            $result['shipping_address'],
            $result['delivery_address'],
            $result['recipient_lastname'],
            $result['recipient_firstname'],
            $result['recipient_phone'],
            $result['weight'],
            $result['package_type'],
            $result['delivery_level'],
            $result['deliverer_number'],
            $result['relay_point_number']
        );
    }

    public function findByOrderNumber(string $orderNumber): ?Commande {
        $sql = "SELECT * FROM commande WHERE order_number = :order_number";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['order_number' => $orderNumber]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        }

        return new Commande(
            $result['order_number'],
            $result['sender_number'],
            new DateTime($result['order_time']),
            $result['paid_amount'],
            $result['payment_method'],
            $result['order_status'],
            $result['shipping_address'],
            $result['delivery_address'],
            $result['recipient_lastname'],
            $result['recipient_firstname'],
            $result['recipient_phone'],
            $result['weight'],
            $result['package_type'],
            $result['delivery_level'],
            $result['deliverer_number'],
            $result['relay_point_number']
        );
    }

    public function updateStatus(int $id, string $status): bool {
        $sql = "UPDATE commande SET order_status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'status' => $status,
            'id' => $id
        ]);
    }

    public function assignDeliverer(int $id, string $delivererNumber): bool {
        $sql = "UPDATE commande SET deliverer_number = :deliverer_number WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'deliverer_number' => $delivererNumber,
            'id' => $id
        ]);
    }

    public function findAll(): array {
        $sql = "SELECT * FROM commande ORDER BY order_time DESC";
        $stmt = $this->db->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $commandes = [];
        foreach ($results as $result) {
            $commandes[] = new Commande(
                $result['order_number'],
                $result['sender_number'],
                new DateTime($result['order_time']),
                $result['paid_amount'],
                $result['payment_method'],
                $result['order_status'],
                $result['shipping_address'],
                $result['delivery_address'],
                $result['recipient_lastname'],
                $result['recipient_firstname'],
                $result['recipient_phone'],
                $result['weight'],
                $result['package_type'],
                $result['delivery_level'],
                $result['deliverer_number'],
                $result['relay_point_number']
            );
        }
        
        return $commandes;
    }
}
<?php

class Order {
    private int $id;
    private string $order_number;
    private int $sender_id;
    private DateTime $order_time;
    private float $paid_amount;
    private string $payment_method;
    private string $shipping_address;
    private string $delivery_address;
    private string $recipient_lastname;
    private string $recipient_firstname;
    private string $recipient_phone;
    private int $weight;
    private string $delivery_level;
    private ?int $deliverer_id;
    private ?int $relay_point_id;

    public function __construct(
        string $order_number,
        int $sender_id,
        DateTime $order_time,
        float $paid_amount,
        string $payment_method,
        string $shipping_address,
        string $delivery_address,
        string $recipient_lastname,
        string $recipient_firstname,
        string $recipient_phone,
        int $weight,
        string $delivery_level,
        ?int $deliverer_id = null,
        ?int $relay_point_id = null
    ) {
        $this->order_number = $order_number;
        $this->sender_id = $sender_id;
        $this->order_time = $order_time;
        $this->paid_amount = $paid_amount;
        $this->payment_method = $payment_method;
        $this->shipping_address = $shipping_address;
        $this->delivery_address = $delivery_address;
        $this->recipient_lastname = $recipient_lastname;
        $this->recipient_firstname = $recipient_firstname;
        $this->recipient_phone = $recipient_phone;
        $this->weight = $weight;
        $this->delivery_level = $delivery_level;
        $this->deliverer_id = $deliverer_id;
        $this->relay_point_id = $relay_point_id;
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getOrderNumber(): string {
        return $this->order_number;
    }

    public function getSenderId(): int {
        return $this->sender_id;
    }

    public function getOrderTime(): DateTime {
        return $this->order_time;
    }

    public function getPaidAmount(): float {
        return $this->paid_amount;
    }

    public function getPaymentMethod(): string {
        return $this->payment_method;
    }

    public function getShippingAddress(): string {
        return $this->shipping_address;
    }

    public function getDeliveryAddress(): string {
        return $this->delivery_address;
    }

    public function getRecipientLastname(): string {
        return $this->recipient_lastname;
    }

    public function getRecipientFirstname(): string {
        return $this->recipient_firstname;
    }

    public function getRecipientPhone(): string {
        return $this->recipient_phone;
    }

    public function getWeight(): int {
        return $this->weight;
    }

    public function getDeliveryLevel(): string {
        return $this->delivery_level;
    }

    public function getDelivererId(): ?int {
        return $this->deliverer_id;
    }

    public function getRelayPointId(): ?int {
        return $this->relay_point_id;
    }

    // Setters
    public function setDelivererId(?int $deliverer_id): void {
        $this->deliverer_id = $deliverer_id;
    }

    public function setRelayPointId(?int $relay_point_id): void {
        $this->relay_point_id = $relay_point_id;
    }
} 
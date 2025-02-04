<?php

class Commande {
    private int $id;
    private string $order_number;
    private string $sender_number;
    private DateTime $order_time;
    private float $paid_amount;
    private string $payment_method;
    private string $order_status;
    private string $shipping_address;
    private string $delivery_address;
    private string $recipient_lastname;
    private string $recipient_firstname;
    private string $recipient_phone;
    private int $weight;
    private string $package_type;
    private string $delivery_level;
    private ?string $deliverer_number;
    private ?int $relay_point_number;
    private DateTime $created_at;
    private DateTime $updated_at;

    public function __construct(
        string $order_number,
        string $sender_number,
        DateTime $order_time,
        float $paid_amount,
        string $payment_method,
        string $order_status,
        string $shipping_address,
        string $delivery_address,
        string $recipient_lastname,
        string $recipient_firstname,
        string $recipient_phone,
        int $weight,
        string $package_type,
        string $delivery_level,
        ?string $deliverer_number = null,
        ?int $relay_point_number = null
    ) {
        $this->order_number = $order_number;
        $this->sender_number = $sender_number;
        $this->order_time = $order_time;
        $this->paid_amount = $paid_amount;
        $this->payment_method = $payment_method;
        $this->order_status = $order_status;
        $this->shipping_address = $shipping_address;
        $this->delivery_address = $delivery_address;
        $this->recipient_lastname = $recipient_lastname;
        $this->recipient_firstname = $recipient_firstname;
        $this->recipient_phone = $recipient_phone;
        $this->weight = $weight;
        $this->package_type = $package_type;
        $this->delivery_level = $delivery_level;
        $this->deliverer_number = $deliverer_number;
        $this->relay_point_number = $relay_point_number;
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getOrderNumber(): string {
        return $this->order_number;
    }

    public function getSenderNumber(): string {
        return $this->sender_number;
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

    public function getOrderStatus(): string {
        return $this->order_status;
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

    public function getPackageType(): string {
        return $this->package_type;
    }

    public function getDeliveryLevel(): string {
        return $this->delivery_level;
    }

    public function getDelivererNumber(): ?string {
        return $this->deliverer_number;
    }

    public function getRelayPointNumber(): ?int {
        return $this->relay_point_number;
    }

    public function getCreatedAt(): DateTime {
        return $this->created_at;
    }

    public function getUpdatedAt(): DateTime {
        return $this->updated_at;
    }

    // Setters
    public function setOrderStatus(string $order_status): void {
        $this->order_status = $order_status;
    }

    public function setDelivererNumber(?string $deliverer_number): void {
        $this->deliverer_number = $deliverer_number;
    }

    public function setRelayPointNumber(?int $relay_point_number): void {
        $this->relay_point_number = $relay_point_number;
    }
}
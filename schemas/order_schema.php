<?php

$create_order_schema = [
    'type' => 'object',
    'required' => [
        'sender_id',
        'order_time',
        'paid_amount',
        'payment_method',
        'shipping_address',
        'delivery_address',
        'recipient_lastname',
        'recipient_firstname',
        'recipient_phone',
        'weight',
        'delivery_level'
    ],
    'properties' => [
        'sender_id' => ['type' => 'integer'],
        'order_time' => ['type' => 'string', 'format' => 'date-time'],
        'paid_amount' => ['type' => 'number', 'minimum' => 0],
        'payment_method' => ['type' => 'string', 'enum' => ['CASH', 'CARD', 'TRANSFER']],
        'shipping_address' => ['type' => 'string', 'minLength' => 1],
        'delivery_address' => ['type' => 'string', 'minLength' => 1],
        'recipient_lastname' => ['type' => 'string', 'minLength' => 1],
        'recipient_firstname' => ['type' => 'string', 'minLength' => 1],
        'recipient_phone' => ['type' => 'string', 'pattern' => '^\+?[0-9]{10,15}$'],
        'weight' => ['type' => 'integer', 'minimum' => 0],
        'delivery_level' => ['type' => 'string', 'enum' => ['STANDARD', 'EXPRESS']],
        'deliverer_id' => ['type' => 'integer'],
        'relay_point_id' => ['type' => 'integer']
    ]
];

$assign_deliverer_schema = [
    'type' => 'object',
    'required' => ['deliverer_id'],
    'properties' => [
        'deliverer_id' => ['type' => 'integer']
    ]
];

$assign_relay_point_schema = [
    'type' => 'object',
    'required' => ['relay_point_id'],
    'properties' => [
        'relay_point_id' => ['type' => 'integer']
    ]
];

$order_response_schema = [
    'type' => 'object',
    'properties' => [
        'id' => ['type' => 'integer'],
        'order_number' => ['type' => 'string'],
        'sender_id' => ['type' => 'integer'],
        'order_time' => ['type' => 'string', 'format' => 'date-time'],
        'paid_amount' => ['type' => 'number'],
        'payment_method' => ['type' => 'string'],
        'shipping_address' => ['type' => 'string'],
        'delivery_address' => ['type' => 'string'],
        'recipient_lastname' => ['type' => 'string'],
        'recipient_firstname' => ['type' => 'string'],
        'recipient_phone' => ['type' => 'string'],
        'weight' => ['type' => 'integer'],
        'delivery_level' => ['type' => 'string'],
        'deliverer_id' => ['type' => ['integer', 'null']],
        'relay_point_id' => ['type' => ['integer', 'null']]
    ]
];

$orders_list_response_schema = [
    'type' => 'array',
    'items' => $order_response_schema
];

function validateOrderSchema($data) {
    $required_fields = [
        'sender_id',
        'paid_amount',
        'payment_method',
        'shipping_address',
        'delivery_address',
        'recipient_lastname',
        'recipient_firstname',
        'recipient_phone',
        'weight',
        'delivery_level'
    ];

    $errors = [];

    // Check required fields
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            $errors[] = "Field '$field' is required";
        }
    }

    // Validate data types and ranges
    if (isset($data['sender_id']) && !is_int($data['sender_id'])) {
        $errors[] = "sender_id must be an integer";
    }

    if (isset($data['paid_amount'])) {
        if (!is_numeric($data['paid_amount']) || $data['paid_amount'] < 0) {
            $errors[] = "paid_amount must be a positive number";
        }
    }

    if (isset($data['weight'])) {
        if (!is_int($data['weight']) || $data['weight'] <= 0) {
            $errors[] = "weight must be a positive integer";
        }
    }

    if (isset($data['delivery_level']) && !in_array($data['delivery_level'], ['fast', 'normal'])) {
        $errors[] = "delivery_level must be either 'fast' or 'normal'";
    }

    if (isset($data['deliverer_id']) && !is_null($data['deliverer_id']) && !is_int($data['deliverer_id'])) {
        $errors[] = "deliverer_id must be an integer or null";
    }

    if (isset($data['relay_point_id']) && !is_null($data['relay_point_id']) && !is_int($data['relay_point_id'])) {
        $errors[] = "relay_point_id must be an integer or null";
    }

    // Phone number validation
    if (isset($data['recipient_phone'])) {
        if (!preg_match('/^\+?[0-9]{10,15}$/', $data['recipient_phone'])) {
            $errors[] = "recipient_phone must be a valid phone number";
        }
    }

    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
} 
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
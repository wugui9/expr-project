<?php


$StorageSchema = [
    'type' => 'object',
    'properties' => [
        'id' => ['type' => 'integer'],
        'city' => ['type' => 'string'],
        'postal_code' => ['type' => 'string'],
        'detailed_address' => ['type' => 'string'],
        'capacity_volume_of_the_warehouse' => ['type' => 'integer'],
        'capacity_weight_of_the_warehouse' => ['type' => 'integer']
    ],
    'required' => ['id', 'city', 'postal_code', 'detailed_address', 'capacity_volume_of_the_warehouse', 'capacity_weight_of_the_warehouse']
];

$ListStorageResp = [
    'type' => 'object',
    'properties' => [
        'storages' => ['type' => 'array', 'items' => ['type' => 'object', 'ref' => 'StorageSchema']]
    ],
    'required' => ['storages']
];

class StorageSchema {
    public function format($storage) {
        return [
            'id' => $storage->id,
            'city' => $storage->city,
            'postal_code' => $storage->postal_code,
            'detailed_address' => $storage->detailed_address,
            'capacity_volume' => $storage->capacity_volume_of_the_warehouse,
            'capacity_weight' => $storage->capacity_weight_of_the_warehouse
        ];
    }
}

?>
<?php

require_once __DIR__ . '/BaseSchema.php';

/**
 * 仓库数据类
 */
class StorageSchema extends BaseSchema {
    /** @var int 仓库ID */
    public int $id = 0;
    
    /** @var string 城市 */
    public string $city = '';
    
    /** @var string 邮政编码 */
    public string $postal_code = '';
    
    /** @var string 详细地址 */
    public string $detailed_address = '';
    
    /** @var int 仓库容积容量 */
    public int $capacity_volume_of_the_warehouse = 0;
    
    /** @var int 仓库重量容量 */
    public int $capacity_weight_of_the_warehouse = 0;

    /**
     * 从实体创建Schema对象
     * @param Storage $entity 仓库实体
     * @return self
     */
    public static function fromEntity(Storage $entity): self {
        $storage = new self();
        $storage->id = $entity->id;
        $storage->city = $entity->city;
        $storage->postal_code = $entity->postal_code;
        $storage->detailed_address = $entity->detailed_address;
        $storage->capacity_volume_of_the_warehouse = $entity->capacity_volume_of_the_warehouse;
        $storage->capacity_weight_of_the_warehouse = $entity->capacity_weight_of_the_warehouse;
        return $storage;
    }

    /**
     * 转换为实体对象
     * @return Storage
     */
    public function toEntity(): Storage {
        $storage = new Storage();
        $storage->id = $this->id;
        $storage->city = $this->city;
        $storage->postal_code = $this->postal_code;
        $storage->detailed_address = $this->detailed_address;
        $storage->capacity_volume_of_the_warehouse = $this->capacity_volume_of_the_warehouse;
        $storage->capacity_weight_of_the_warehouse = $this->capacity_weight_of_the_warehouse;
        return $storage;
    }

    /**
     * 验证数据是否有效
     * @return bool
     */
    public function validate(): bool {
        return !empty($this->city) 
            && !empty($this->postal_code) 
            && !empty($this->detailed_address)
            && $this->capacity_volume_of_the_warehouse > 0
            && $this->capacity_weight_of_the_warehouse > 0;
    }
}

/**
 * 仓库列表响应类
 */
class ListStorageResponse extends BaseSchema {
    /** @var StorageSchema[] 仓库列表 */
    public array $storages = [];

    /**
     * 创建响应对象
     * @param StorageSchema[] $storages 仓库列表
     * @return self
     */
    public static function create(array $storages): self {
        $response = new self();
        $response->storages = $storages;
        return $response;
    }

    /**
     * 转换为数组
     * @return array<string,array<string,mixed>>
     */
    public function toArray(): array {
        return [
            'storages' => array_map(fn(StorageSchema $storage) => $storage->toArray(), $this->storages)
        ];
    }
}

class CreateStorageRequest extends BaseSchema {
    public string $city = '';
    public string $postal_code = '';
    public string $detailed_address = '';
    public int $capacity_volume_of_the_warehouse = 0;
    public int $capacity_weight_of_the_warehouse = 0;

    /**
     * 验证数据是否有效
     * @return bool
     */
    public function validate(): bool {
        return !empty($this->city) 
            && !empty($this->postal_code) 
            && !empty($this->detailed_address)
            && $this->capacity_volume_of_the_warehouse > 0
            && $this->capacity_weight_of_the_warehouse > 0;
    }
}

?>
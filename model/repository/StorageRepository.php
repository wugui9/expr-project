<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/../entities/Storage.php';

class StorageRepository extends BaseRepository
{
    public function __construct($connection) 
    {
        parent::__construct($connection, 'storage');
    }

    /**
     * Convert database row to Storage entity
     * @param array $row Database row
     * @return Storage
     */
    private function mapToEntity($row): Storage
    {
        if (!$row) {
            return null;
        }

        $storage = new Storage();
        $storage->id = (int)$row['id'];
        $storage->city = $row['city'];
        $storage->postal_code = $row['postal_code'];
        $storage->detailed_address = $row['detailed_address'];
        $storage->latitude = (float)$row['latitude'];
        $storage->longitude = (float)$row['longitude'];
        $storage->capacity_volume_of_the_warehouse = (int)$row['capacity_volume_of_the_warehouse'];
        $storage->capacity_weight_of_the_warehouse = (int)$row['capacity_weight_of_the_warehouse'];
        
        return $storage;
    }

    /**
     * Override findById to return Storage entity
     * @param int $id
     * @return Storage|null
     */
    public function findById($id): ?Storage
    {
        $row = parent::findById($id);
        return $this->mapToEntity($row);
    }

    /**
     * Override findAll to return array of Storage entities
     * @return Storage[]
     */
    public function findAll(): array
    {
        $rows = parent::findAll();
        $storages = [];
        
        foreach ($rows as $row) {
            $storages[] = $this->mapToEntity($row);
        }
        
        return $storages;
    }

    /**
     * Override findBy to return array of Storage entities
     * @param array $criteria
     * @return Storage[]
     */
    public function findBy($criteria): array
    {
        $rows = parent::findBy($criteria);
        $storages = [];
        
        foreach ($rows as $row) {
            $storages[] = $this->mapToEntity($row);
        }
        
        return $storages;
    }

    /**
     * Find storages by city
     * @param string $city
     * @return Storage[]
     */
    public function findByCity(string $city): array
    {
        return $this->findBy(['city' => $city]);
    }

    /**
     * Find storages with available capacity
     * @param int $requiredVolume
     * @param int $requiredWeight
     * @return Storage[]
     */
    public function findAvailableStorages(int $requiredVolume, int $requiredWeight): array
    {
        if ($this->db == null) {
            return [];
        }

        $request = "SELECT * FROM {$this->table} 
                   WHERE capacity_volume_of_the_warehouse >= :volume 
                   AND capacity_weight_of_the_warehouse >= :weight";
                   
        $statement = $this->db->prepare($request);
        $statement->execute([
            'volume' => $requiredVolume,
            'weight' => $requiredWeight
        ]);
        
        $rows = $statement->fetchAll();
        $storages = [];
        
        foreach ($rows as $row) {
            $storages[] = $this->mapToEntity($row);
        }
        
        return $storages;
    }
}

<?php
require_once __DIR__ . '/../php/DBModel.php';

/**
 * BaseRepository class that contains common database operations
 * Other repository classes will extend this base class to inherit common functionality
 * 
 * @template T
 * @author: Assistant
 * @date: 2024
 */
abstract class BaseRepository
{
    protected $db;
    protected $table;

    public function __construct($connection, $table) 
    {
        $this->db = $connection;
        $this->table = $table;
    }

    /**
     * Convert database row to entity
     * @param array $row
     * @return T
     */
    abstract protected function mapToEntity(array $row);

    /**
     * Find record by ID
     * @param int $id
     * @return T|null
     */
    public function findById(int $id)
    {
        if ($this->db == null) {
            return null;
        }

        $request = "SELECT * FROM `{$this->table}` WHERE id = :id";
        $statement = $this->db->prepare($request);
        $statement->execute(['id' => $id]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $this->mapToEntity($result) : null;
    }

    /**
     * Get all records from table
     * @return T[]
     */
    public function findAll(): array
    {
        if ($this->db == null) {
            return [];
        }

        $request = "SELECT * FROM `{$this->table}`";
        $statement = $this->db->prepare($request);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map([$this, 'mapToEntity'], $results);
    }

    /**
     * Find records by specific criteria
     * @param array $criteria Associative array of column => value pairs
     * @return T[]
     */
    public function findBy(array $criteria): array
    {
        if ($this->db == null) {
            return [];
        }

        $whereClause = [];
        foreach ($criteria as $key => $value) {
            $whereClause[] = "$key = :$key";
        }
        $whereClause = implode(' AND ', $whereClause);
        
        $request = "SELECT * FROM `{$this->table}` WHERE $whereClause";
        $statement = $this->db->prepare($request);
        $statement->execute($criteria);
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map([$this, 'mapToEntity'], $results);
    }

    /**
     * Create new record
     * @param array $data Associative array of column => value pairs
     * @return bool
     */
    public function create(array $data): bool
    {
        if ($this->db == null) {
            return false;
        }

        $columns = implode(', ', array_keys($data));
        $values = ':' . implode(', :', array_keys($data));
        
        $request = "INSERT INTO `{$this->table}` ($columns) VALUES ($values)";
        $statement = $this->db->prepare($request);
        return $statement->execute($data);
    }

    /**
     * Update record by ID
     * @param int $id
     * @param array $data Associative array of column => value pairs
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        if ($this->db == null) {
            return false;
        }

        $setClause = [];
        foreach ($data as $key => $value) {
            $setClause[] = "$key = :$key";
        }
        $setClause = implode(', ', $setClause);
        
        $request = "UPDATE `{$this->table}` SET $setClause WHERE id = :id";
        $data['id'] = $id;
        
        $statement = $this->db->prepare($request);
        return $statement->execute($data);
    }

    /**
     * Delete record by ID
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        if ($this->db == null) {
            return false;
        }

        $request = "DELETE FROM `{$this->table}` WHERE id = :id";
        $statement = $this->db->prepare($request);
        return $statement->execute(['id' => $id]);
    }
}

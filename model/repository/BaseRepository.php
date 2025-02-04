<?php
require_once __DIR__ . '/../php/DBModel.php';

/**
 * BaseRepository class that contains common database operations
 * Other repository classes will extend this base class to inherit common functionality
 * 
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
     * Find record by ID
     * @param int $id
     * @return array|null
     */
    public function findById($id)
    {
        if ($this->db == null) {
            return null;
        }

        $request = "SELECT * FROM {$this->table} WHERE id = :id";
        $statement = $this->db->prepare($request);
        $statement->execute(['id' => $id]);
        return $statement->fetch();
    }

    /**
     * Get all records from table
     * @return array
     */
    public function findAll()
    {
        if ($this->db == null) {
            return [];
        }

        $request = "SELECT * FROM {$this->table}";
        $statement = $this->db->prepare($request);
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * Create new record
     * @param array $data Associative array of column => value pairs
     * @return bool
     */
    public function create($data)
    {
        if ($this->db == null) {
            return false;
        }

        $columns = implode(', ', array_keys($data));
        $values = ':' . implode(', :', array_keys($data));
        
        $request = "INSERT INTO {$this->table} ($columns) VALUES ($values)";
        $statement = $this->db->prepare($request);
        return $statement->execute($data);
    }

    /**
     * Update record by ID
     * @param int $id
     * @param array $data Associative array of column => value pairs
     * @return bool
     */
    public function update($id, $data)
    {
        if ($this->db == null) {
            return false;
        }

        $setClause = [];
        foreach ($data as $key => $value) {
            $setClause[] = "$key = :$key";
        }
        $setClause = implode(', ', $setClause);
        
        $request = "UPDATE {$this->table} SET $setClause WHERE id = :id";
        $data['id'] = $id;
        
        $statement = $this->db->prepare($request);
        return $statement->execute($data);
    }

    /**
     * Delete record by ID
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        if ($this->db == null) {
            return false;
        }

        $request = "DELETE FROM {$this->table} WHERE id = :id";
        $statement = $this->db->prepare($request);
        return $statement->execute(['id' => $id]);
    }

    /**
     * Find records by specific criteria
     * @param array $criteria Associative array of column => value pairs
     * @return array
     */
    public function findBy($criteria)
    {
        if ($this->db == null) {
            return [];
        }

        $whereClause = [];
        foreach ($criteria as $key => $value) {
            $whereClause[] = "$key = :$key";
        }
        $whereClause = implode(' AND ', $whereClause);
        
        $request = "SELECT * FROM {$this->table} WHERE $whereClause";
        $statement = $this->db->prepare($request);
        $statement->execute($criteria);
        return $statement->fetchAll();
    }
}

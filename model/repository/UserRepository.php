<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/../entities/user.php';

/**
 * @extends BaseRepository<User>
 */
class UserRepository extends BaseRepository
{
    public function __construct($connection) 
    {
        parent::__construct($connection, 'user');
    }

    /**
     * Find user by email
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        $result = $this->findBy(['email' => $email]);
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Verify password for user
     * @param User $user
     * @param string $password
     * @return bool
     */
    public function verifyPassword(User $user, string $password): bool
    {
        $hashedPassword = hash('sha256', $password . $user->salt);
        return $hashedPassword === $user->password;
    }

    /**
     * Convert database row to User entity
     * @param array $row
     * @return User
     */
    protected function mapToEntity(array $row): User
    {
        $user = new User();
        $user->id = (int)$row['id'];
        $user->lastname = $row['last_name'];
        $user->firstname = $row['first_name'];
        $user->phone = $row['phone'];
        $user->email = $row['email'];
        $user->password = $row['password'];
        $user->salt = $row['salt'];
        $user->role = $row['role'];
        $user->address = $row['address'] ?? null;
        
        return $user;
    }
} 
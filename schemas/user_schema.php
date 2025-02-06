<?php

require_once __DIR__ . '/BaseSchema.php';
require_once __DIR__ . '/../model/entities/user.php';

/**
 * 用户数据类
 */
class UserSchema extends BaseSchema {
    /** @var int 用户ID */
    public int $id = 0;
    
    /** @var string 姓 */
    public string $last_name = '';
    
    /** @var string 名 */
    public string $first_name = '';
    
    /** @var string 电话 */
    public string $phone = '';
    
    /** @var string 邮箱 */
    public string $email = '';
    
    /** @var string 密码 */
    public string $password = '';
    
    /** @var string 盐值 */
    public string $salt = '';
    
    /** @var string 角色 */
    public string $role = 'CUSTOMER';

    /**
     * 从实体创建Schema对象
     * @param User $entity 用户实体
     * @return self
     */
    public static function fromEntity(User $entity): self {
        $user = new self();
        $user->id = $entity->id;
        $user->last_name = $entity->lastname;
        $user->first_name = $entity->firstname;
        $user->phone = $entity->phone;
        $user->email = $entity->email;
        $user->password = $entity->password;
        $user->salt = $entity->salt;
        $user->role = $entity->role;
        return $user;
    }

    /**
     * 转换为实体对象
     * @return User
     */
    public function toEntity(): User {
        $user = new User();
        $user->id = $this->id;
        $user->lastname = $this->last_name;
        $user->firstname = $this->first_name;
        $user->phone = $this->phone;
        $user->email = $this->email;
        $user->password = $this->password;
        $user->salt = $this->salt;
        $user->role = $this->role;
        return $user;
    }

    /**
     * 验证数据是否有效
     * @return bool
     */
    public function validate(): bool {
        return !empty($this->last_name)
            && !empty($this->first_name)
            && !empty($this->phone)
            && !empty($this->email)
            && filter_var($this->email, FILTER_VALIDATE_EMAIL)
            && !empty($this->password)
            && strlen($this->password) >= 8
            && !empty($this->salt)
            && strlen($this->salt) === 32
            && in_array($this->role, ['ADMIN', 'CUSTOMER', 'DELIVERY_DRIVER']);
    }
}

/**
 * 登录请求类
 */
class LoginRequest extends BaseSchema {
    /** @var string 邮箱 */
    public string $email = '';
    
    /** @var string 密码 */
    public string $password = '';

    /**
     * 从数组创建请求对象
     * @param array<string,mixed> $data
     * @return static
     */
    public static function fromArray(array $data): static {
        $instance = new static();
        $instance->email = $data['email'] ?? '';
        $instance->password = $data['password'] ?? '';
        return $instance;
    }

    /**
     * 验证数据是否有效
     * @return bool
     */
    public function validate(): bool {
        return !empty($this->email) 
            && filter_var($this->email, FILTER_VALIDATE_EMAIL)
            && !empty($this->password);
    }
}

/**
 * 登录响应类
 */
class LoginResponse extends BaseSchema {
    /** @var int 用户ID */
    public int $id = 0;
    
    /** @var string 邮箱 */
    public string $email = '';
    
    /** @var string 名 */
    public string $firstname = '';
    
    /** @var string 姓 */
    public string $lastname = '';
    
    /** @var string 角色 */
    public string $role = '';

    /**
     * 从实体创建响应对象
     * @param User $user 用户实体
     * @param string $token JWT令牌 (用于设置cookie，不返回给客户端)
     * @return self
     */
    public static function fromEntity(User $user, string $token): self {
        $instance = new self();
        $instance->id = $user->id;
        $instance->email = $user->email;
        $instance->firstname = $user->firstname;
        $instance->lastname = $user->lastname;
        $instance->role = $user->role;
        return $instance;
    }

    /**
     * 转换为数组
     * @return array<string,mixed>
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'role' => $this->role
        ];
    }
} 
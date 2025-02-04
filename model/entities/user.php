<?php
declare(strict_types=1);

class User {
    public int $id;
    public string $lastname;
    public string $firstname;
    public string $phone;
    public string $email;
    public string $password;
    public string $salt;
    public int $role;
}

?>
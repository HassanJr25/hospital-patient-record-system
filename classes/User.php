<?php


abstract class User
{
  
    // ENCAPSULATION:

    protected int $userId;
    protected string $username;
    protected string $email;
    protected string $fullName;
    protected string $role;

    
     // CONSTRUCTOR

    public function __construct(int $userId, string $username, string $email, string $fullName, string $role)
    {
        $this->userId = $userId;
        $this->username = $username;
        $this->email = $email;
        $this->fullName = $fullName;
        $this->role = $role;
    }


    // Simple public "getter" methods.

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getRole(): string
    {
        return $this->role;
    }

     // ABSTRACT METHOD

    abstract public function getPermissions(): array;

    /**
     * A normal (non-abstract) shared method.
     * Both Admin and Doctor will use this exact same implementation,
     * since it doesn't need to differ between roles.
     */
    public function getDisplayLabel(): string
    {
        return $this->fullName . ' (' . ucfirst($this->role) . ')';
    }
}

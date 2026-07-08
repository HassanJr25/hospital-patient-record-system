<?php
/**
 * Admin.php
 
 */

require_once __DIR__ . '/User.php';

class Admin extends User
{
    
    public function __construct(int $userId, string $username, string $email, string $fullName)
    {
        // Note: we hardcode 'admin' as the role, since this class
        // only ever represents admin accounts.
        parent::__construct($userId, $username, $email, $fullName, 'admin');
    }

   
    public function getPermissions(): array
    {
        return [
            'manage_patients'      => true,
            'manage_medical_records' => true,
            'manage_users'          => true,   // Only Admins can do this
            'view_reports'           => true,
        ];
    }

   
    public function canManageUsers(): bool
    {
        return true;
    }
}

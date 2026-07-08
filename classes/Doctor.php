<?php

require_once __DIR__ . '/User.php';

class Doctor extends User
{

    protected string $specialization;

    public function __construct(int $userId, string $username, string $email, string $fullName, string $specialization = 'General')
    {
        parent::__construct($userId, $username, $email, $fullName, 'doctor');
        $this->specialization = $specialization;
    }

    public function getSpecialization(): string
    {
        return $this->specialization;
    }


    public function getPermissions(): array
    {
        return [
            'manage_patients'         => true,
            'manage_medical_records'  => true,
            'manage_users'            => false,  // Doctors cannot do this
            'view_reports'            => true,
        ];
    }
}

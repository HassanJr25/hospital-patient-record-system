<?php


require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Admin.php';
require_once __DIR__ . '/Doctor.php';

class UserManager
{
     // createUser()

    public static function createUser(
        string $username,
        string $email,
        string $plainPassword,
        string $fullName,
        string $role
    ): bool {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare(
            'INSERT INTO users (username, email, password, full_name, role, is_active)
             VALUES (:username, :email, :password, :full_name, :role, 1)'
        );

        return $stmt->execute([
            'username' => $username,
            'email'    => $email,
            'password' => password_hash($plainPassword, PASSWORD_BCRYPT),
            'full_name'=> $fullName,
            'role'     => $role,
        ]);
    }

     // usernameExists() / emailExists()
   
    public static function usernameExists(string $username): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = :username');
        $stmt->execute(['username' => $username]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function emailExists(string $email): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        return (int)$stmt->fetchColumn() > 0;
    }

     // findAll()

    public static function findAll(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('SELECT * FROM users ORDER BY created_at DESC');

        $users = [];
        foreach ($stmt->fetchAll() as $row) {
            if ($row['role'] === 'admin') {
                $user = new Admin((int)$row['user_id'], $row['username'], $row['email'], $row['full_name']);
            } else {
                $user = new Doctor((int)$row['user_id'], $row['username'], $row['email'], $row['full_name']);
            }
            $users[] = ['user' => $user, 'is_active' => (bool)$row['is_active']];
        }

        return $users;
    }

    /**
     * toggleActive()
     * -------------------------------------------------------------
     * Enables/disables a login WITHOUT deleting the account - useful
     * for a doctor who leaves the hospital but whose historical
     * medical records (doctor_id foreign key) must still make sense.
     * This mirrors the soft-delete philosophy we used for patients
     * and medical records.
     * -------------------------------------------------------------
     */
    public static function toggleActive(int $userId, bool $active): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE users SET is_active = :active WHERE user_id = :id');
        return $stmt->execute(['active' => $active ? 1 : 0, 'id' => $userId]);
    }
}

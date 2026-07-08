<?php
/**
 * Auth.php
 
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Admin.php';
require_once __DIR__ . '/Doctor.php';

class Auth
{
   
    public static function attemptLogin(string $username, string $password): bool
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare(
            'SELECT user_id, username, email, password, full_name, role, is_active
             FROM users
             WHERE username = :username'
        );
        $stmt->execute(['username' => $username]);
        $row = $stmt->fetch();

        // No matching username found.
        if (!$row) {
            return false;
        }

        // Account has been disabled by an admin.
        if ((int)$row['is_active'] !== 1) {
            return false;
        }

       
        if (!password_verify($password, $row['password'])) {
            return false;
        }

    
        session_regenerate_id(true);

        // Login successful - store the essential info in the session.
        // We only store what we need (not the password hash!).
        $_SESSION['user_id']   = $row['user_id'];
        $_SESSION['username']  = $row['username'];
        $_SESSION['full_name'] = $row['full_name'];
        $_SESSION['role']      = $row['role'];

        return true;
    }

   
    public static function logout(): void
    {
        // Clear all session variables.
        $_SESSION = [];

        // Delete the session cookie from the browser, if one exists.
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Destroy the session data on the server side.
        session_destroy();
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

   
    public static function getCurrentUser(): ?User
    {
        if (!self::isLoggedIn()) {
            return null;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = :id');
        $stmt->execute(['id' => $_SESSION['user_id']]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        if ($row['role'] === 'admin') {
            return new Admin((int)$row['user_id'], $row['username'], $row['email'], $row['full_name']);
        }

        return new Doctor((int)$row['user_id'], $row['username'], $row['email'], $row['full_name']);
    }
}

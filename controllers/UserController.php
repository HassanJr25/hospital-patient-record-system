<?php

require_once __DIR__ . '/../includes/admin_check.php';
require_once __DIR__ . '/../classes/UserManager.php';
require_once __DIR__ . '/../classes/Validator.php';

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

// ACTION: add

if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $fullName = trim($_POST['full_name'] ?? '');
    $role = $_POST['role'] ?? '';

    $errors = [];

    if (Validator::isEmpty($username)) {
        $errors[] = 'Username is required.';
    } elseif (UserManager::usernameExists($username)) {
        $errors[] = 'That username is already taken.';
    }

    if (Validator::isEmpty($email) || !Validator::isValidEmail($email)) {
        $errors[] = 'A valid email is required.';
    } elseif (UserManager::emailExists($email)) {
        $errors[] = 'That email is already registered.';
    }

    if (!Validator::minLength($password, 8)) {
        $errors[] = 'Password must be at least 8 characters long.';
    }

    if (Validator::isEmpty($fullName) || !Validator::isAlphaSpace($fullName)) {
        $errors[] = 'A valid full name is required (letters and spaces only).';
    }

    if (!in_array($role, ['admin', 'doctor'], true)) {
        $errors[] = 'Please select a valid role.';
    }

    if (!empty($errors)) {
        $_SESSION['user_errors'] = $errors;
        $_SESSION['user_old_input'] = $_POST;
        header('Location: ' . BASE_URL . 'views/users/add.php');
        exit;
    }

    UserManager::createUser($username, $email, $password, $fullName, $role);

    $_SESSION['user_success'] = 'User account created successfully.';
    header('Location: ' . BASE_URL . 'views/users/list.php');
    exit;
}

// ACTION: toggle_active

if ($action === 'toggle_active') {
    $userId = (int)($_GET['id'] ?? 0);
    $newState = (int)($_GET['state'] ?? 1) === 1;

    if ($userId > 0) {
        // Safety check: prevent an admin from disabling their OWN account
        // and accidentally locking themselves out.
        if ($userId === (int)$_SESSION['user_id']) {
            $_SESSION['user_errors'] = ['You cannot disable your own account.'];
        } else {
            UserManager::toggleActive($userId, $newState);
            $_SESSION['user_success'] = 'User status updated successfully.';
        }
    }

    header('Location: ' . BASE_URL . 'views/users/list.php');
    exit;
}

header('Location: ' . BASE_URL . 'views/users/list.php');
exit;

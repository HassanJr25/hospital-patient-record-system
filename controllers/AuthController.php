<?php
/**
 * AuthController.php
 * ---------------------------------------------------------------------
 * A CONTROLLER's job: receive form input from the browser, hand it to
 * the right CLASS to process, then decide where to send the user next.
 * Controllers should contain very little logic themselves - they just
 * coordinate between the view (HTML form) and the class (Auth.php).
 * ---------------------------------------------------------------------
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Validator.php';

// Only process if the form was actually submitted via POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // ---------------------------------------------------------
    // FORM VALIDATION (Functional Requirement: form validation)
    // We check the input BEFORE touching the database at all.
    // ---------------------------------------------------------
    $errors = [];

    if (Validator::isEmpty($username)) {
        $errors[] = 'Username is required.';
    }

    if (Validator::isEmpty($password)) {
        $errors[] = 'Password is required.';
    }

    if (!empty($errors)) {
        $_SESSION['login_errors'] = $errors;
        header('Location: ' . BASE_URL . 'views/auth/login.php');
        exit;
    }

    // ---------------------------------------------------------
    // Delegate the actual login check to the Auth class.
    // The controller does NOT know or care how login works
    // internally - it just asks Auth and reacts to true/false.
    // ---------------------------------------------------------
    $success = Auth::attemptLogin($username, $password);

    if ($success) {
        header('Location: ' . BASE_URL . 'views/dashboard.php');
        exit;
    } else {
        $_SESSION['login_errors'] = ['Invalid username or password.'];
        header('Location: ' . BASE_URL . 'views/auth/login.php');
        exit;
    }
}

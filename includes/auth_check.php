<?php
/**
 * auth_check.php
 * ---------------------------------------------------------------------
 * This is a small, reusable "guard" script.
 * Any page that should ONLY be visible to logged-in users starts with:
 *     require_once __DIR__ . '/../includes/auth_check.php';
 *
 * If the person is NOT logged in, we immediately redirect them to the
 * login page and STOP the rest of the page from running (exit).
 * ---------------------------------------------------------------------
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';

if (!Auth::isLoggedIn()) {
    header('Location: ' . BASE_URL . 'views/auth/login.php');
    exit;
}

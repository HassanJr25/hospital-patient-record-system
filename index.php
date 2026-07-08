<?php
/**
 * index.php
 * ---------------------------------------------------------------------
 * The "front door" of the app. If someone visits the project's base
 * URL directly, we send them either to the dashboard (if logged in)
 * or to the login page (if not).
 * ---------------------------------------------------------------------
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/classes/Auth.php';

if (Auth::isLoggedIn()) {
    header('Location: ' . BASE_URL . 'views/dashboard.php');
} else {
    header('Location: ' . BASE_URL . 'views/auth/login.php');
}
exit;

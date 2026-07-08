<?php
/**
 * logout.php
 * ---------------------------------------------------------------------
 * Sits at the project ROOT so it's easy to link to from anywhere:
 * BASE_URL . 'logout.php'
 * ---------------------------------------------------------------------
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/classes/Auth.php';

Auth::logout();

header('Location: ' . BASE_URL . 'views/auth/login.php');
exit;

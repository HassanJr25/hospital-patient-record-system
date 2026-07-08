<?php

// ENVIRONMENT MODE

define('APP_ENV', 'development');


ini_set('display_errors', 0);

require_once __DIR__ . '/../includes/error_handler.php';

// Start the session on every page that includes this file.
// A "session" lets PHP remember that a user is logged in as they move
// between pages (login state is NOT automatically remembered otherwise,
// because HTTP is "stateless" - each page load is independent).
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Base URL of your project - used for links/redirects.
// CHANGE this if your folder name is different on your machine.
define('BASE_URL', 'http://localhost/hospital-patient-records/');


define('ENCRYPTION_KEY', 'YkT8y3Fv9wCpQe62SjXhZ4nBmR1LdGu7');

// The encryption "cipher" method we will use throughout the app.
define('ENCRYPTION_METHOD', 'AES-256-CBC');

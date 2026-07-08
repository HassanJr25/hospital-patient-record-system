<?php
/**
 * config.example.php
 * ---------------------------------------------------------------------
 * This is a TEMPLATE, not the real config file the app actually uses.
 *
 * WHY does this file exist?
 * config/config.php contains environment-specific values (BASE_URL,
 * ENCRYPTION_KEY) that are DIFFERENT on your local machine vs. the
 * live AWS server. If we tracked the real config.php in Git, every
 * `git push`/`git pull` would fight over whose values are "correct" -
 * exactly the problem we ran into during Stage 14 deployment.
 *
 * THE FIX: config.php and database.php are now listed in .gitignore
 * (never tracked by Git). This file (config.example.php) IS tracked,
 * as a reference showing what config.php should contain.
 *
 * HOW TO USE THIS FILE:
 * 1. Copy it: cp config/config.example.php config/config.php
 * 2. Edit config/config.php (NOT this file) with your real values.
 * 3. config/config.php will then be ignored by Git from now on -
 *    safe to customize per environment without ever causing conflicts.
 * ---------------------------------------------------------------------
 */

define('APP_ENV', 'development'); // 'development' or 'production'

error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../includes/error_handler.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CHANGE this to match your environment:
//   Local XAMPP example: http://localhost/hospital-patient-records/
//   AWS example:          http://YOUR-SERVER-IP/
define('BASE_URL', 'http://localhost/hospital-patient-records/');

// CHANGE this to a real 32-character secret key for your environment.
// Must be exactly 32 characters (256 bits) for AES-256-CBC.
define('ENCRYPTION_KEY', 'REPLACE_WITH_YOUR_OWN_32CHAR_KEY');

define('ENCRYPTION_METHOD', 'AES-256-CBC');

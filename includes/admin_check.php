<?php
/**
 * admin_check.php
 * ---------------------------------------------------------------------
 * AUTHORIZATION guard (not to be confused with auth_check.php, which
 * only handles AUTHENTICATION).
 *
 * AUTHENTICATION = "Are you logged in at all?"      (auth_check.php)
 * AUTHORIZATION  = "Are you ALLOWED to do THIS?"     (this file)
 *
 * This file assumes auth_check.php has ALREADY run (so we know someone
 * is logged in) and additionally confirms they are specifically an
 * Admin before letting them proceed. A logged-in Doctor who tries to
 * visit an Admin-only page gets redirected away, even though they are
 * fully authenticated.
 * ---------------------------------------------------------------------
 */

require_once __DIR__ . '/auth_check.php'; // must be logged in first
require_once __DIR__ . '/../classes/Auth.php';

$currentUser = Auth::getCurrentUser();

// getPermissions() is the SAME polymorphic method from Stage 7/8 -
// here we finally put manage_users to real use.
if (!$currentUser || $currentUser->getPermissions()['manage_users'] !== true) {
    $_SESSION['patient_errors'] = ['You do not have permission to access that page.'];
    header('Location: ' . BASE_URL . 'views/dashboard.php');
    exit;
}

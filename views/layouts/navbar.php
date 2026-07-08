<?php
/**
 * navbar.php
 * ---------------------------------------------------------------------
 * A simple shared navigation bar, included on every protected page
 * after the header. Keeps navigation consistent without repeating
 * the same HTML on every page.
 *
 * The "Users" link below only appears for Admins - this is a small
 * UI-level AUTHORIZATION touch: Doctors never even SEE a link to a
 * page they aren't allowed to use. (The real enforcement still
 * happens server-side in admin_check.php - hiding the link is just
 * good UX, never a substitute for the actual server-side check.)
 * ---------------------------------------------------------------------
 */
require_once __DIR__ . '/../../classes/Auth.php';
$navCurrentUser = Auth::getCurrentUser();
$navIsAdmin = $navCurrentUser && $navCurrentUser->getPermissions()['manage_users'] === true;
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 no-print">
    <div class="container">
        <a class="navbar-brand" href="<?php echo BASE_URL; ?>views/dashboard.php">Hospital PRMS</a>
        <div class="navbar-nav">
            <a class="nav-link" href="<?php echo BASE_URL; ?>views/dashboard.php">Dashboard</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>views/patients/list.php">Patients</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>views/reports/patient_report.php">Patient Report</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>views/reports/medical_report.php">Medical Records Report</a>
            <?php if ($navIsAdmin): ?>
                <a class="nav-link" href="<?php echo BASE_URL; ?>views/users/list.php">Users</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

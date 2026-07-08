<?php
require_once __DIR__ . '/../includes/auth_check.php'; // blocks access if not logged in
require_once __DIR__ . '/../classes/Auth.php';

// getCurrentUser() returns an Admin OR Doctor object - we don't need
// to know which one to call its methods. That's polymorphism at work.
$currentUser = Auth::getCurrentUser();
$permissions = $currentUser->getPermissions();

require_once __DIR__ . '/layouts/header.php';
require_once __DIR__ . '/layouts/navbar.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Welcome, <?php echo htmlspecialchars($currentUser->getDisplayLabel()); ?></h4>
        <a href="<?php echo BASE_URL; ?>logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
    </div>

    <a href="<?php echo BASE_URL; ?>views/patients/list.php" class="btn btn-primary mb-4">Go to Patient Management</a>

    <div class="card">
        <div class="card-header">Your permissions (role: <?php echo htmlspecialchars($currentUser->getRole()); ?>)</div>
        <ul class="list-group list-group-flush">
            <?php foreach ($permissions as $permission => $allowed): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <?php echo htmlspecialchars(str_replace('_', ' ', ucfirst($permission))); ?>
                    <?php if ($allowed): ?>
                        <span class="badge bg-success">Allowed</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Not allowed</span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="mt-4 text-muted">
        Patient Management and Medical Records modules will appear here in later stages.
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>

<?php
require_once __DIR__ . '/../../includes/admin_check.php';
require_once __DIR__ . '/../../classes/UserManager.php';

$users = UserManager::findAll();

$successMessage = $_SESSION['user_success'] ?? null;
$errors = $_SESSION['user_errors'] ?? [];
unset($_SESSION['user_success'], $_SESSION['user_errors']);

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>User Accounts</h4>
        <a href="<?php echo BASE_URL; ?>views/users/add.php" class="btn btn-primary">+ Add New User</a>
    </div>

    <?php if ($successMessage): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white">
            <thead class="table-light">
                <tr>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $entry): ?>
                    <?php $u = $entry['user']; ?>
                    <tr>
                        <td><?php echo htmlspecialchars($u->getFullName()); ?></td>
                        <td><?php echo htmlspecialchars($u->getUsername()); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($u->getRole())); ?></td>
                        <td>
                            <?php if ($entry['is_active']): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Disabled</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($u->getUserId() === (int)$_SESSION['user_id']): ?>
                                <span class="text-muted small">(This is you)</span>
                            <?php elseif ($entry['is_active']): ?>
                                <a href="<?php echo BASE_URL; ?>controllers/UserController.php?action=toggle_active&id=<?php echo $u->getUserId(); ?>&state=0"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Disable this account? They will no longer be able to log in.');">Disable</a>
                            <?php else: ?>
                                <a href="<?php echo BASE_URL; ?>controllers/UserController.php?action=toggle_active&id=<?php echo $u->getUserId(); ?>&state=1"
                                   class="btn btn-sm btn-outline-success">Enable</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

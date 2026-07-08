<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Auth.php';

// If already logged in, no need to see the login page again.
if (Auth::isLoggedIn()) {
    header('Location: ' . BASE_URL . 'views/dashboard.php');
    exit;
}

// Grab any validation/login errors passed from AuthController.php,
// then clear them so they don't show again on the next visit.
$errors = $_SESSION['login_errors'] ?? [];
unset($_SESSION['login_errors']);

require_once __DIR__ . '/../layouts/header.php';
?>

<div class="login-card">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h4 class="text-center mb-4">Hospital Patient Record System</h4>
            <h6 class="text-center text-muted mb-4">Login</h6>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?php echo BASE_URL; ?>controllers/AuthController.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

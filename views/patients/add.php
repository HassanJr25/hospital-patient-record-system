<?php
require_once __DIR__ . '/../../includes/auth_check.php';

$errors = $_SESSION['patient_errors'] ?? [];
$old = $_SESSION['patient_old_input'] ?? [];
unset($_SESSION['patient_errors'], $_SESSION['patient_old_input']);

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="container py-4">
    <h4 class="mb-4">Add New Patient</h4>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?php echo BASE_URL; ?>controllers/PatientController.php?action=add">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control"
                               value="<?php echo htmlspecialchars($old['full_name'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">NRIC / National ID</label>
                        <input type="text" name="nric" class="form-control"
                               value="<?php echo htmlspecialchars($old['nric'] ?? ''); ?>" required>
                        <div class="form-text">This will be stored encrypted in the database.</div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob" class="form-control"
                               value="<?php echo htmlspecialchars($old['dob'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select" required>
                            <option value="">-- Select --</option>
                            <?php foreach (['male', 'female', 'other'] as $g): ?>
                                <option value="<?php echo $g; ?>" <?php echo (($old['gender'] ?? '') === $g) ? 'selected' : ''; ?>>
                                    <?php echo ucfirst($g); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control"
                               value="<?php echo htmlspecialchars($old['phone'] ?? ''); ?>" required>
                        <div class="form-text">This will be stored encrypted in the database.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control"
                               value="<?php echo htmlspecialchars($old['address'] ?? ''); ?>">
                        <div class="form-text">This will be stored encrypted in the database.</div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Save Patient</button>
                <a href="<?php echo BASE_URL; ?>views/patients/list.php" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

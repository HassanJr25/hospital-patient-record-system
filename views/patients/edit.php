<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../classes/Patient.php';

$patientId = (int)($_GET['id'] ?? 0);
$patient = Patient::findById($patientId);

if (!$patient) {
    $_SESSION['patient_errors'] = ['Patient not found.'];
    header('Location: ' . BASE_URL . 'views/patients/list.php');
    exit;
}

$errors = $_SESSION['patient_errors'] ?? [];
unset($_SESSION['patient_errors']);

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="container py-4">
    <h4 class="mb-4">Edit Patient</h4>

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
            <!-- Notice we pass the patient_id as a hidden field so
                 PatientController.php knows WHICH patient to update. -->
            <form method="POST" action="<?php echo BASE_URL; ?>controllers/PatientController.php?action=update">
                <input type="hidden" name="patient_id" value="<?php echo $patient->getPatientId(); ?>">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control"
                               value="<?php echo htmlspecialchars($patient->getFullName()); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">NRIC / National ID</label>
                        <!-- This value was DECRYPTED by Patient::findById() just now,
                             purely for display in this authorized edit form. -->
                        <input type="text" name="nric" class="form-control"
                               value="<?php echo htmlspecialchars($patient->getNric()); ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob" class="form-control"
                               value="<?php echo htmlspecialchars($patient->getDob()); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select" required>
                            <?php foreach (['male', 'female', 'other'] as $g): ?>
                                <option value="<?php echo $g; ?>" <?php echo ($patient->getGender() === $g) ? 'selected' : ''; ?>>
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
                               value="<?php echo htmlspecialchars($patient->getPhone()); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control"
                               value="<?php echo htmlspecialchars($patient->getAddress()); ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Patient</button>
                <a href="<?php echo BASE_URL; ?>views/patients/list.php" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

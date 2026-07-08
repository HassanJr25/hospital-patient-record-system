<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../classes/Patient.php';

$patientId = (int)($_GET['patient_id'] ?? 0);
$patient = Patient::findById($patientId);

if (!$patient) {
    $_SESSION['patient_errors'] = ['Patient not found.'];
    header('Location: ' . BASE_URL . 'views/patients/list.php');
    exit;
}

$errors = $_SESSION['record_errors'] ?? [];
unset($_SESSION['record_errors']);

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="container py-4">
    <div class="mb-2">
        <a href="<?php echo BASE_URL; ?>views/medical_records/list.php?patient_id=<?php echo $patientId; ?>" class="text-decoration-none">&larr; Back to Medical History</a>
    </div>

    <h4 class="mb-4">Add Medical Record for <?php echo htmlspecialchars($patient->getFullName()); ?></h4>

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
            <form method="POST" action="<?php echo BASE_URL; ?>controllers/MedicalRecordController.php?action=add">
                <input type="hidden" name="patient_id" value="<?php echo $patientId; ?>">

                <div class="mb-3">
                    <label class="form-label">Visit Date</label>
                    <input type="date" name="visit_date" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Diagnosis</label>
                    <textarea name="diagnosis" class="form-control" rows="3" required></textarea>
                    <div class="form-text">This will be stored encrypted in the database.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Treatment / Prescription Notes</label>
                    <textarea name="treatment" class="form-control" rows="3"></textarea>
                    <div class="form-text">This will be stored encrypted in the database.</div>
                </div>

                <button type="submit" class="btn btn-primary">Save Record</button>
                <a href="<?php echo BASE_URL; ?>views/medical_records/list.php?patient_id=<?php echo $patientId; ?>" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

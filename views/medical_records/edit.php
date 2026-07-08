<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../classes/MedicalRecord.php';
require_once __DIR__ . '/../../classes/Patient.php';

$recordId = (int)($_GET['id'] ?? 0);
$record = MedicalRecord::findById($recordId);

if (!$record) {
    $_SESSION['record_errors'] = ['Medical record not found.'];
    header('Location: ' . BASE_URL . 'views/patients/list.php');
    exit;
}

$patient = Patient::findById($record->getPatientId());

$errors = $_SESSION['record_errors'] ?? [];
unset($_SESSION['record_errors']);

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="container py-4">
    <div class="mb-2">
        <a href="<?php echo BASE_URL; ?>views/medical_records/list.php?patient_id=<?php echo $record->getPatientId(); ?>" class="text-decoration-none">&larr; Back to Medical History</a>
    </div>

    <h4 class="mb-4">Edit Medical Record for <?php echo htmlspecialchars($patient->getFullName()); ?></h4>

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
            <form method="POST" action="<?php echo BASE_URL; ?>controllers/MedicalRecordController.php?action=update">
                <input type="hidden" name="record_id" value="<?php echo $record->getRecordId(); ?>">
                <input type="hidden" name="patient_id" value="<?php echo $record->getPatientId(); ?>">

                <div class="mb-3">
                    <label class="form-label">Visit Date</label>
                    <input type="date" name="visit_date" class="form-control"
                           value="<?php echo htmlspecialchars($record->getVisitDate()); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Diagnosis</label>
                    <textarea name="diagnosis" class="form-control" rows="3" required><?php echo htmlspecialchars($record->getDiagnosis()); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Treatment / Prescription Notes</label>
                    <textarea name="treatment" class="form-control" rows="3"><?php echo htmlspecialchars($record->getTreatment()); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Update Record</button>
                <a href="<?php echo BASE_URL; ?>views/medical_records/list.php?patient_id=<?php echo $record->getPatientId(); ?>" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

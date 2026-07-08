<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../classes/MedicalRecord.php';
require_once __DIR__ . '/../../classes/Patient.php';

$patientId = (int)($_GET['patient_id'] ?? 0);
$patient = Patient::findById($patientId);

if (!$patient) {
    $_SESSION['patient_errors'] = ['Patient not found.'];
    header('Location: ' . BASE_URL . 'views/patients/list.php');
    exit;
}

$records = MedicalRecord::findByPatientId($patientId);

$successMessage = $_SESSION['record_success'] ?? null;
unset($_SESSION['record_success']);

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="container py-4">
    <div class="mb-2">
        <a href="<?php echo BASE_URL; ?>views/patients/list.php" class="text-decoration-none">&larr; Back to Patient List</a>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Medical History: <?php echo htmlspecialchars($patient->getFullName()); ?></h4>
        <a href="<?php echo BASE_URL; ?>views/medical_records/add.php?patient_id=<?php echo $patientId; ?>"
           class="btn btn-primary">+ Add Medical Record</a>
    </div>

    <?php if ($successMessage): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white">
            <thead class="table-light">
                <tr>
                    <th>Visit Date</th>
                    <th>Diagnosis</th>
                    <th>Treatment</th>
                    <th>Recorded By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                    <tr><td colspan="5" class="text-center text-muted">No medical records found for this patient.</td></tr>
                <?php else: ?>
                    <?php foreach ($records as $record): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record->getVisitDate()); ?></td>
                            <!-- Decrypted on the fly by MedicalRecord::hydrate() -->
                            <td><?php echo htmlspecialchars($record->getDiagnosis()); ?></td>
                            <td><?php echo htmlspecialchars($record->getTreatment()); ?></td>
                            <td><?php echo htmlspecialchars($record->getDoctorName()); ?></td>
                            <td>
                                <a href="<?php echo BASE_URL; ?>views/medical_records/edit.php?id=<?php echo $record->getRecordId(); ?>"
                                   class="btn btn-sm btn-outline-primary">Edit</a>
                                <a href="<?php echo BASE_URL; ?>controllers/MedicalRecordController.php?action=delete&id=<?php echo $record->getRecordId(); ?>&patient_id=<?php echo $patientId; ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Are you sure you want to delete this medical record?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

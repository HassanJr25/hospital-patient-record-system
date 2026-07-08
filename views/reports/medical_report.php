<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../classes/Report.php';

$records = Report::generateMedicalRecordsReport();

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="container py-4">
    <div class="no-print d-flex justify-content-between align-items-center mb-4">
        <h4>Medical Records Report</h4>
        <div>
            <button onclick="window.print()" class="btn btn-outline-secondary">Print</button>
        </div>
    </div>

    <div class="d-none d-print-block mb-3">
        <h4>Medical Records Report</h4>
        <p class="text-muted">Generated on <?php echo date('Y-m-d H:i'); ?></p>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered bg-white">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Visit Date</th>
                    <th>Diagnosis</th>
                    <th>Treatment</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                    <tr><td colspan="6" class="text-center text-muted">No medical records found.</td></tr>
                <?php else: ?>
                    <?php foreach ($records as $record): ?>
                        <tr>
                            <td><?php echo $record->getRecordId(); ?></td>
                            <td><?php echo htmlspecialchars($record->getPatientName()); ?></td>
                            <td><?php echo htmlspecialchars($record->getDoctorName()); ?></td>
                            <td><?php echo htmlspecialchars($record->getVisitDate()); ?></td>
                            <td><?php echo htmlspecialchars($record->getDiagnosis()); ?></td>
                            <td><?php echo htmlspecialchars($record->getTreatment()); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <p class="text-muted">Total records: <?php echo count($records); ?></p>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

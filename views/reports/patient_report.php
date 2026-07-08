<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../classes/Report.php';

$patients = Report::generatePatientListReport();

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="container py-4">
    <div class="no-print d-flex justify-content-between align-items-center mb-4">
        <h4>Patient List Report</h4>
        <div>
            <button onclick="window.print()" class="btn btn-outline-secondary">Print</button>
        </div>
    </div>

    <!-- This heading only shows when printed, giving the printed page a proper title/date -->
    <div class="d-none d-print-block mb-3">
        <h4>Patient List Report</h4>
        <p class="text-muted">Generated on <?php echo date('Y-m-d H:i'); ?></p>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered bg-white">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Date of Birth</th>
                    <th>Gender</th>
                    <th>Phone</th>
                    <th>Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($patients)): ?>
                    <tr><td colspan="6" class="text-center text-muted">No patients found.</td></tr>
                <?php else: ?>
                    <?php foreach ($patients as $patient): ?>
                        <tr>
                            <td><?php echo $patient->getPatientId(); ?></td>
                            <td><?php echo htmlspecialchars($patient->getFullName()); ?></td>
                            <td><?php echo htmlspecialchars($patient->getDob()); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($patient->getGender())); ?></td>
                            <td><?php echo htmlspecialchars($patient->getPhone()); ?></td>
                            <td><?php echo htmlspecialchars($patient->getAddress()); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <p class="text-muted">Total patients: <?php echo count($patients); ?></p>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

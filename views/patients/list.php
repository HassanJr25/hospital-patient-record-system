<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../classes/Patient.php';

// -------------------------------------------------------------------
// SEARCH (Functional Requirement: Search Patient)
// This is a simple read-only GET request, so we handle it directly
// here rather than routing it through PatientController.php.
// -------------------------------------------------------------------
$keyword = trim($_GET['keyword'] ?? '');

if ($keyword !== '') {
    $patients = Patient::search($keyword);
} else {
    $patients = Patient::findAll();
}

$successMessage = $_SESSION['patient_success'] ?? null;
unset($_SESSION['patient_success']);

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Patient List</h4>
        <a href="<?php echo BASE_URL; ?>views/patients/add.php" class="btn btn-primary">+ Add New Patient</a>
    </div>

    <?php if ($successMessage): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>

    <!-- Search form: simple GET request back to this same page -->
    <form method="GET" action="" class="mb-4 d-flex gap-2">
        <input type="text" name="keyword" class="form-control" placeholder="Search by patient name..."
               value="<?php echo htmlspecialchars($keyword); ?>">
        <button type="submit" class="btn btn-outline-secondary">Search</button>
        <?php if ($keyword !== ''): ?>
            <a href="<?php echo BASE_URL; ?>views/patients/list.php" class="btn btn-outline-secondary">Clear</a>
        <?php endif; ?>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Date of Birth</th>
                    <th>Gender</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($patients)): ?>
                    <tr><td colspan="7" class="text-center text-muted">No patients found.</td></tr>
                <?php else: ?>
                    <?php foreach ($patients as $patient): ?>
                        <tr>
                            <td><?php echo $patient->getPatientId(); ?></td>
                            <td><?php echo htmlspecialchars($patient->getFullName()); ?></td>
                            <td><?php echo htmlspecialchars($patient->getDob()); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($patient->getGender())); ?></td>
                            <!-- These values are DECRYPTED on the fly by Patient::hydrate() -
                                 the database itself never stores them in plain text. -->
                            <td><?php echo htmlspecialchars($patient->getPhone()); ?></td>
                            <td><?php echo htmlspecialchars($patient->getAddress()); ?></td>
                            <td>
                                <a href="<?php echo BASE_URL; ?>views/medical_records/list.php?patient_id=<?php echo $patient->getPatientId(); ?>"
                                   class="btn btn-sm btn-outline-success">History</a>
                                <a href="<?php echo BASE_URL; ?>views/patients/edit.php?id=<?php echo $patient->getPatientId(); ?>"
                                   class="btn btn-sm btn-outline-primary">Edit</a>
                                <a href="<?php echo BASE_URL; ?>controllers/PatientController.php?action=delete&id=<?php echo $patient->getPatientId(); ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Are you sure you want to delete this patient?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

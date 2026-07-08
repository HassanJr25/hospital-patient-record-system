<?php


require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../classes/MedicalRecord.php';
require_once __DIR__ . '/../classes/Patient.php';
require_once __DIR__ . '/../classes/Validator.php';
require_once __DIR__ . '/../classes/Auth.php';

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

function validateRecordInput(array $data): array
{
    $errors = [];

    if (Validator::isEmpty($data['diagnosis'] ?? '')) {
        $errors[] = 'Diagnosis is required.';
    }

    if (Validator::isEmpty($data['visit_date'] ?? '')) {
        $errors[] = 'Visit date is required.';
    } elseif (!Validator::isValidDate($data['visit_date'])) {
        $errors[] = 'Visit date must be a valid date (YYYY-MM-DD).';
    }

    return $errors;
}


// ACTION: add
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $patientId = (int)($_POST['patient_id'] ?? 0);
    $errors = validateRecordInput($_POST);

    // Make sure the patient actually exists before attaching a record to it.
    if (!Patient::findById($patientId)) {
        $errors[] = 'Invalid patient.';
    }

    if (!empty($errors)) {
        $_SESSION['record_errors'] = $errors;
        header('Location: ' . BASE_URL . 'views/medical_records/add.php?patient_id=' . $patientId);
        exit;
    }

    $currentUser = Auth::getCurrentUser();

    $record = new MedicalRecord(
        $patientId,
        $currentUser->getUserId(), // the logged-in doctor/admin is recorded as the author
        trim($_POST['diagnosis']),
        trim($_POST['treatment'] ?? ''),
        $_POST['visit_date']
    );

    $record->save();

    $_SESSION['record_success'] = 'Medical record added successfully.';
    header('Location: ' . BASE_URL . 'views/medical_records/list.php?patient_id=' . $patientId);
    exit;
}


// ACTION: update

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $recordId = (int)($_POST['record_id'] ?? 0);
    $patientId = (int)($_POST['patient_id'] ?? 0);
    $errors = validateRecordInput($_POST);

    $existing = MedicalRecord::findById($recordId);
    if (!$existing) {
        $errors[] = 'Medical record not found.';
    }

    if (!empty($errors)) {
        $_SESSION['record_errors'] = $errors;
        header('Location: ' . BASE_URL . 'views/medical_records/edit.php?id=' . $recordId);
        exit;
    }

    // patient_id and doctor_id stay as they were originally - only the
    // clinical content and visit date can be edited (see MedicalRecord::update()).
    $record = new MedicalRecord(
        $existing->getPatientId(),
        $existing->getDoctorId(),
        trim($_POST['diagnosis']),
        trim($_POST['treatment'] ?? ''),
        $_POST['visit_date'],
        $recordId
    );

    $record->update();

    $_SESSION['record_success'] = 'Medical record updated successfully.';
    header('Location: ' . BASE_URL . 'views/medical_records/list.php?patient_id=' . $patientId);
    exit;
}


// ACTION: delete

if ($action === 'delete') {
    $recordId = (int)($_GET['id'] ?? 0);
    $patientId = (int)($_GET['patient_id'] ?? 0);

    if ($recordId > 0) {
        MedicalRecord::deleteById($recordId);
        $_SESSION['record_success'] = 'Medical record deleted successfully.';
    }

    header('Location: ' . BASE_URL . 'views/medical_records/list.php?patient_id=' . $patientId);
    exit;
}

header('Location: ' . BASE_URL . 'views/patients/list.php');
exit;

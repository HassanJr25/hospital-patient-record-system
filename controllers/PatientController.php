<?php

require_once __DIR__ . '/../includes/auth_check.php'; // must be logged in
require_once __DIR__ . '/../classes/Patient.php';
require_once __DIR__ . '/../classes/Validator.php';
require_once __DIR__ . '/../classes/Auth.php';

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

/**
 * validatePatientInput()

 */
function validatePatientInput(array $data): array
{
    $errors = [];

    if (Validator::isEmpty($data['full_name'] ?? '')) {
        $errors[] = 'Full name is required.';
    } elseif (!Validator::isAlphaSpace($data['full_name'])) {
        $errors[] = 'Full name may only contain letters and spaces.';
    }

    if (Validator::isEmpty($data['nric'] ?? '')) {
        $errors[] = 'NRIC/ID number is required.';
    }

    if (Validator::isEmpty($data['dob'] ?? '')) {
        $errors[] = 'Date of birth is required.';
    } elseif (!Validator::isValidDate($data['dob'])) {
        $errors[] = 'Date of birth must be a valid date (YYYY-MM-DD).';
    }

    if (Validator::isEmpty($data['gender'] ?? '') || !in_array($data['gender'], ['male', 'female', 'other'], true)) {
        $errors[] = 'Please select a valid gender.';
    }

    if (Validator::isEmpty($data['phone'] ?? '')) {
        $errors[] = 'Phone number is required.';
    }

    return $errors;
}

// ACTION: add

if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $errors = validatePatientInput($_POST);

    if (!empty($errors)) {
        $_SESSION['patient_errors'] = $errors;
        $_SESSION['patient_old_input'] = $_POST; // so the form can be re-filled
        header('Location: ' . BASE_URL . 'views/patients/add.php');
        exit;
    }

    $currentUser = Auth::getCurrentUser();

    $patient = new Patient(
        trim($_POST['full_name']),
        trim($_POST['nric']),
        $_POST['dob'],
        $_POST['gender'],
        trim($_POST['phone']),
        trim($_POST['address'] ?? ''),
        $currentUser->getUserId()
    );

    $patient->save();

    $_SESSION['patient_success'] = 'Patient added successfully.';
    header('Location: ' . BASE_URL . 'views/patients/list.php');
    exit;
}

// ACTION: update
if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $errors = validatePatientInput($_POST);
    $patientId = (int)($_POST['patient_id'] ?? 0);

    if (!empty($errors)) {
        $_SESSION['patient_errors'] = $errors;
        header('Location: ' . BASE_URL . 'views/patients/edit.php?id=' . $patientId);
        exit;
    }

    $existing = Patient::findById($patientId);
    if (!$existing) {
        $_SESSION['patient_errors'] = ['Patient not found.'];
        header('Location: ' . BASE_URL . 'views/patients/list.php');
        exit;
    }

    // We rebuild a Patient object with the SAME id, so update() knows
    // which row to modify (see the WHERE patient_id = :id in Patient::update()).
    $patient = new Patient(
        trim($_POST['full_name']),
        trim($_POST['nric']),
        $_POST['dob'],
        $_POST['gender'],
        trim($_POST['phone']),
        trim($_POST['address'] ?? ''),
        (int)$_SESSION['user_id'], // registered_by is not changed by update() - see Patient::update()
        $patientId
    );

    $patient->update();

    $_SESSION['patient_success'] = 'Patient updated successfully.';
    header('Location: ' . BASE_URL . 'views/patients/list.php');
    exit;
}

// ACTION: delete
if ($action === 'delete') {
    $patientId = (int)($_GET['id'] ?? 0);

    if ($patientId > 0) {
        Patient::deleteById($patientId);
        $_SESSION['patient_success'] = 'Patient deleted successfully.';
    }

    header('Location: ' . BASE_URL . 'views/patients/list.php');
    exit;
}

// If no valid action matched, just go back to the list.
header('Location: ' . BASE_URL . 'views/patients/list.php');
exit;

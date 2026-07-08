<?php
/**
 * run_tests.php
 * ---------------------------------------------------------------------
 * A SIMPLE, hand-written test script (not using a formal testing
 * framework like PHPUnit, to keep things beginner-friendly and easy
 * to explain). It checks that our core OOP and security logic behaves
 * correctly, and prints a clear PASS/FAIL report.
 *
 * HOW TO RUN THIS:
 *   From the command line, inside the project folder:
 *     php tests/run_tests.php
 *
 *   Or, if you don't have command-line access, you can visit it in
 *   your browser: http://localhost/hospital-patient-records/tests/run_tests.php
 *   (the <pre> tag below makes sure line breaks display correctly
 *   in the browser, since HTML normally ignores plain line breaks)
 * ---------------------------------------------------------------------
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Admin.php';
require_once __DIR__ . '/../classes/Doctor.php';
require_once __DIR__ . '/../classes/EncryptionHelper.php';
require_once __DIR__ . '/../classes/Validator.php';

// Only add the <pre> wrapper when this script is viewed through a
// web browser (not when run via command line, where it's unnecessary).
$isBrowser = php_sapi_name() !== 'cli';
if ($isBrowser) {
    echo '<pre style="font-family: Consolas, monospace; font-size: 14px; padding: 20px;">';
}

$passCount = 0;
$failCount = 0;

/**
 * A tiny helper that prints PASS or FAIL for one test, and keeps count.
 */
function checkTest(string $description, bool $condition): void
{
    global $passCount, $failCount;

    if ($condition) {
        echo "[PASS] $description" . PHP_EOL;
        $passCount++;
    } else {
        echo "[FAIL] $description" . PHP_EOL;
        $failCount++;
    }
}

echo "=====================================================" . PHP_EOL;
echo " Hospital Patient Record Management System - Test Run " . PHP_EOL;
echo "=====================================================" . PHP_EOL . PHP_EOL;

// ---------------------------------------------------------------------
// TEST GROUP 1: Inheritance & Polymorphism
// ---------------------------------------------------------------------
echo "-- Inheritance & Polymorphism --" . PHP_EOL;

$admin = new Admin(1, 'admin', 'admin@hospital.local', 'System Administrator');
$doctor = new Doctor(2, 'drjohn', 'drjohn@hospital.local', 'Dr. John Mwakalinga', 'Cardiologist');

checkTest('Admin is an instance of User (inheritance)', $admin instanceof User);
checkTest('Doctor is an instance of User (inheritance)', $doctor instanceof User);

$adminPerms = $admin->getPermissions();
$doctorPerms = $doctor->getPermissions();

checkTest('Admin.getPermissions() grants manage_users', $adminPerms['manage_users'] === true);
checkTest('Doctor.getPermissions() denies manage_users (polymorphism)', $doctorPerms['manage_users'] === false);
checkTest('Both roles can manage patients', $adminPerms['manage_patients'] === true && $doctorPerms['manage_patients'] === true);

echo PHP_EOL;

// ---------------------------------------------------------------------
// TEST GROUP 2: Encryption / Decryption
// ---------------------------------------------------------------------
echo "-- Encryption & Decryption --" . PHP_EOL;

$original = '19900514-1234';
$encrypted = EncryptionHelper::encrypt($original);
$decrypted = EncryptionHelper::decrypt($encrypted);

checkTest('Encrypted value differs from original plain text', $encrypted !== $original);
checkTest('Decrypted value matches the original plain text', $decrypted === $original);

$encryptedAgain = EncryptionHelper::encrypt($original);
checkTest('Encrypting the same value twice produces different ciphertext (random IV)', $encrypted !== $encryptedAgain);

echo PHP_EOL;

// ---------------------------------------------------------------------
// TEST GROUP 3: Form Validation
// ---------------------------------------------------------------------
echo "-- Form Validation --" . PHP_EOL;

checkTest('Validator detects an empty string', Validator::isEmpty('') === true);
checkTest('Validator accepts a non-empty string', Validator::isEmpty('Jane') === false);
checkTest('Validator accepts a valid email', Validator::isValidEmail('user@example.com') === true);
checkTest('Validator rejects an invalid email', Validator::isValidEmail('not-an-email') === false);
checkTest('Validator accepts a valid date (YYYY-MM-DD)', Validator::isValidDate('1990-05-14') === true);
checkTest('Validator rejects an invalid date', Validator::isValidDate('14-05-1990') === false);
checkTest('Validator accepts letters and spaces only', Validator::isAlphaSpace('Jane Wanjiru') === true);
checkTest('Validator rejects a name containing numbers', Validator::isAlphaSpace('Jane123') === false);

echo PHP_EOL;

// ---------------------------------------------------------------------
// SUMMARY
// ---------------------------------------------------------------------
echo "=====================================================" . PHP_EOL;
echo " RESULTS: $passCount passed, $failCount failed" . PHP_EOL;
echo "=====================================================" . PHP_EOL;

if ($isBrowser) {
    echo '</pre>';
}

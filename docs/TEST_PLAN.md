# Test Plan & Testing Evidence
## Hospital Patient Record Management System

This document records the manual and automated testing performed on the system, covering every module defined in the project scope.

---

## 1. Automated Tests

An automated test script (`tests/run_tests.php`) verifies core OOP and security logic that underpins the whole system. Run it with:

```
php tests/run_tests.php
```

| # | Test | Expected Result | Actual Result | Status |
|---|---|---|---|---|
| A1 | `Admin` is an instance of `User` | True (inheritance works) | True | PASS |
| A2 | `Doctor` is an instance of `User` | True (inheritance works) | True | PASS |
| A3 | `Admin::getPermissions()` grants `manage_users` | True | True | PASS |
| A4 | `Doctor::getPermissions()` denies `manage_users` | False (polymorphism: different behavior per subclass) | False | PASS |
| A5 | Both roles can manage patients | True for both | True for both | PASS |
| A6 | Encrypted value differs from plain text | True | True | PASS |
| A7 | Decrypted value matches original plain text | True | True | PASS |
| A8 | Encrypting the same value twice produces different ciphertext | True (random IV) | True | PASS |
| A9 | Validator detects empty strings | True | True | PASS |
| A10 | Validator accepts valid emails, rejects invalid ones | True/False respectively | True/False | PASS |
| A11 | Validator accepts valid dates, rejects invalid ones | True/False respectively | True/False | PASS |
| A12 | Validator accepts letters/spaces only, rejects numbers in names | True/False respectively | True/False | PASS |

**Result: 16/16 automated tests passed.**

---

## 2. Manual Functional Testing

### 2.1 Authentication Module

| # | Test Case | Steps | Expected Result | Status |
|---|---|---|---|---|
| M1 | Valid login (Admin) | Log in with `admin` / `Admin@123` | Redirected to dashboard, shown Admin permissions | PASS |
| M2 | Valid login (Doctor) | Log in with `drjohn` / `Doctor@123` | Redirected to dashboard, shown Doctor permissions (no manage_users) | PASS |
| M3 | Invalid login | Log in with wrong password | Generic "Invalid username or password" error shown | PASS |
| M4 | Logout | Click Logout | Session destroyed, redirected to login page | PASS |
| M5 | Session guard | Visit `views/dashboard.php` directly after logging out | Redirected back to login page | PASS |

### 2.2 Patient Management Module

| # | Test Case | Steps | Expected Result | Status |
|---|---|---|---|---|
| P1 | Add patient | Fill and submit Add Patient form | Patient appears in list; success message shown | PASS |
| P2 | View patient list | Navigate to Patients | All non-deleted patients displayed | PASS |
| P3 | Update patient | Edit an existing patient's phone number | Change reflected in list after saving | PASS |
| P4 | Delete patient (soft delete) | Click Delete on a patient, confirm | Patient disappears from list; row remains in DB with `is_deleted = 1` | PASS |
| P5 | Search patient | Type partial name into search box | Matching patients shown; non-matching hidden | PASS |
| P6 | Sensitive field encryption | Add a patient, inspect `patients` table in phpMyAdmin | `nric_encrypted`, `phone_encrypted`, `address_encrypted` show ciphertext, not plain text | PASS |

### 2.3 Medical Records Module

| # | Test Case | Steps | Expected Result | Status |
|---|---|---|---|---|
| R1 | Add medical record | From a patient's History page, add a new record | Record appears in that patient's history | PASS |
| R2 | View medical history | Open History for a patient | All records for that patient shown, newest first, with doctor name | PASS |
| R3 | Update medical record | Edit an existing record's diagnosis | Change reflected after saving | PASS |
| R4 | Delete medical record (soft delete) | Delete a record, confirm | Record disappears from history; row remains in DB with `is_deleted = 1` | PASS |
| R5 | Sensitive field encryption | Inspect `medical_records` table in phpMyAdmin | `diagnosis_encrypted`, `treatment_encrypted` show ciphertext | PASS |

### 2.4 Reports Module

| # | Test Case | Steps | Expected Result | Status |
|---|---|---|---|---|
| RP1 | Patient List Report | Open Patient Report page | All active patients listed with decrypted contact details | PASS |
| RP2 | Medical Records Report | Open Medical Records Report page | All active records listed with patient + doctor names | PASS |
| RP3 | Print report | Click Print on either report | Browser print preview opens; navbar/buttons hidden | PASS |

### 2.5 Security & Error Handling

| # | Test Case | Steps | Expected Result | Status |
|---|---|---|---|---|
| S1 | SQL Injection attempt | Enter `' OR '1'='1` into login username field | Login rejected (PDO prepared statements prevent injection) | PASS |
| S2 | XSS attempt | Add a patient with name `<script>alert(1)</script>` | Displayed as harmless text in list, not executed (via `htmlspecialchars()`) | PASS |
| S3 | Error handling | Temporarily break DB config, reload a page | Friendly generic error page shown, no raw PHP error; error logged privately to `logs/app_errors.log` | PASS |
| S4 | Role-based permission difference | Compare Admin vs Doctor dashboard | Doctor correctly shown "Not allowed" for Manage Users | PASS |

---

## 3. Known Limitations Identified During Testing
- Search only matches on patient full name (by design - encrypted fields cannot be searched directly; see Security Design section).
- No CSRF token protection implemented in this version (documented as a recommendation for future improvement).
- Encryption key is stored directly in `config/config.php` rather than an external secrets manager (acceptable for an academic project; documented as a production recommendation).

# Hospital Patient Record Management System

A web-based Hospital Patient Record Management System built with **PHP (OOP)** and **MySQL**, developed as an individual assignment for the Internet and Web Development course (BIT Year 2).

No PHP framework is used — the project follows a custom, lightweight MVC-like structure.

## Features
- User Authentication (login, logout, session management) with role-based accounts (Admin, Doctor)
- Patient Management (Add, View, Update, Delete, Search) with soft-delete
- Medical Records Management (Add, View History, Update, Delete) linked to patients
- Reports (Patient List Report, Medical Records Report) with print support
- Sensitive data (NRIC, phone, address, diagnosis, treatment) encrypted at rest using AES-256-CBC
- Passwords hashed with bcrypt
- SQL injection protection via PDO prepared statements
- XSS protection via output escaping
- Centralized error handling and logging

## Tech Stack
- PHP 8+ (OOP, no framework)
- MySQL (3NF-normalized schema)
- PDO for database access
- Bootstrap 5 (via CDN) for UI styling

## Project Structure
```
config/         Database & app configuration
classes/        Core OOP classes (User, Admin, Doctor, Patient, MedicalRecord, etc.)
controllers/    Handle form submissions and route to the right logic
views/          HTML/PHP presentation layer
includes/       Shared guards and helpers (session check, error handler)
assets/         CSS/JS/images
database/       SQL schema
docs/           Test plan and documentation
tests/          Simple automated test script
```

## Setup Instructions

1. Install [XAMPP](https://www.apachefriends.org) (or any Apache + PHP 8+ + MySQL stack).
2. Clone this repository into your `htdocs` folder:
   ```
   git clone <your-repo-url> hospital-patient-records
   ```
3. Start Apache and MySQL in XAMPP.
4. Import the database schema:
   - Open phpMyAdmin (`http://localhost/phpmyadmin`)
   - Click **Import** → choose `database/hospital_db.sql` → click **Go**
5. Visit `http://localhost/hospital-patient-records/` in your browser.
6. Log in with a demo account:
   - Admin: `admin` / `Admin@123`
   - Doctor: `drjohn` / `Doctor@123`

## Running Automated Tests
```
php tests/run_tests.php
```

## Security Notes
- The encryption key is stored in `config/config.php` for simplicity in this academic context. In production, it should be stored in an environment variable or secrets manager.
- Patient full names are stored in plain text (not encrypted) to support search functionality, while NRIC, phone, address, diagnosis, and treatment fields are encrypted.
- Deletions are soft deletes (`is_deleted` flag) to preserve historical records, consistent with real-world healthcare data retention practices.

## Author
BIT Year 2 Individual Assignment — Internet and Web Development

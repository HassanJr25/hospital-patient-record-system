
CREATE DATABASE IF NOT EXISTS hospital_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE hospital_db;

-- Table 1: users

CREATE TABLE users (
    user_id     INT AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50)  NOT NULL UNIQUE,
    email       VARCHAR(100) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,          -- bcrypt hash, NOT reversible encryption
    full_name   VARCHAR(100) NOT NULL,
    role        ENUM('admin', 'doctor') NOT NULL,
    is_active   TINYINT(1)   NOT NULL DEFAULT 1, -- 1 = active, 0 = disabled account
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table 2: patients

CREATE TABLE patients (
    patient_id          INT AUTO_INCREMENT PRIMARY KEY,
    full_name           VARCHAR(100) NOT NULL,        -- plaintext: needed for search/sort
    nric_encrypted      VARCHAR(255) NOT NULL,        -- encrypted National ID/NRIC
    dob                 DATE NOT NULL,
    gender              ENUM('male', 'female', 'other') NOT NULL,
    phone_encrypted     VARCHAR(255) NOT NULL,        -- encrypted phone number
    address_encrypted   TEXT NULL,                    -- encrypted address
    registered_by       INT NOT NULL,                 -- FK to users.user_id
    is_deleted          TINYINT(1) NOT NULL DEFAULT 0, -- soft delete flag
    created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_patients_registered_by
        FOREIGN KEY (registered_by) REFERENCES users(user_id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Index to speed up name searches (Search Patient feature)
CREATE INDEX idx_patients_full_name ON patients(full_name);


-- Table 3: medical_records
CREATE TABLE medical_records (
    record_id              INT AUTO_INCREMENT PRIMARY KEY,
    patient_id              INT NOT NULL,             -- FK to patients.patient_id
    doctor_id                INT NOT NULL,             -- FK to users.user_id
    diagnosis_encrypted      TEXT NOT NULL,            -- encrypted diagnosis
    treatment_encrypted      TEXT NULL,                -- encrypted treatment/prescription notes
    visit_date               DATE NOT NULL,
    is_deleted                TINYINT(1) NOT NULL DEFAULT 0, -- soft delete flag
    created_at                TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_records_patient
        FOREIGN KEY (patient_id) REFERENCES patients(patient_id)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT fk_records_doctor
        FOREIGN KEY (doctor_id) REFERENCES users(user_id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Index to speed up "view medical history for a patient" queries
CREATE INDEX idx_records_patient_id ON medical_records(patient_id);


-- Default Admin account
-- NOTE: This is the ONE place we insert a pre-hashed password directly
-- via SQL for initial setup. Password is: Admin@123
-- Hash generated using PHP's password_hash() with PASSWORD_BCRYPT.
INSERT INTO users (username, email, password, full_name, role, is_active)
VALUES (
    'admin',
    'admin@hospital.local',
    '$2b$10$nre2xWUcF9Xv4jXB8L3UFe.SXA0Qh4bzFsA9D0/UQO01D2UzOwHKm', -- Admin@123
    'System Administrator',
    'admin',
    1
);

-- Default Doctor account
-- Password is: Doctor@123
INSERT INTO users (username, email, password, full_name, role, is_active)
VALUES (
    'drjohn',
    'drjohn@hospital.local',
    '$2b$10$oqI3/ok8G/nGBuu20IWDzuISPyXOAG7dK0j59oQ0/ITVQyZbsmJxy', -- Doctor@123
    'Dr. John Mwakalinga',
    'doctor',
    1
);


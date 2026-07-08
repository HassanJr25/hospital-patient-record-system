<?php
/**
 * Patient.php
 * ---------------------------------------------------------------------
 * Represents a single patient AND handles all database operations for
 * patients (Add, View, Update, Delete, Search). We chose this combined
 * "entity + data access" style (instead of a separate model layer) to
 * keep the project simple, as agreed in Stage 5.
 *
 * ENCAPSULATION REMINDER:
 * All properties are "private". Outside code cannot do
 * $patient->nric directly - it must use getNric() (which we deliberately
 * do NOT provide, to avoid accidentally leaking decrypted data - see
 * getDecryptedNric() instead, which makes the decryption explicit).
 * ---------------------------------------------------------------------
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/EncryptionHelper.php';

class Patient
{
    private ?int $patientId;
    private string $fullName;
    private string $nric;      // plain text in memory, encrypted in DB
    private string $dob;
    private string $gender;
    private string $phone;     // plain text in memory, encrypted in DB
    private string $address;   // plain text in memory, encrypted in DB
    private int $registeredBy;

    /**
     * CONSTRUCTOR
     * -------------------------------------------------------------
     * Accepts PLAIN (unencrypted) values. Encryption happens later,
     * only at the moment we actually write to the database (in
     * save()). This keeps the class easy to reason about: while an
     * object is alive in memory, its properties are always readable
     * plain text; encryption is purely a STORAGE concern.
     * -------------------------------------------------------------
     */
    public function __construct(
        string $fullName,
        string $nric,
        string $dob,
        string $gender,
        string $phone,
        string $address,
        int $registeredBy,
        ?int $patientId = null
    ) {
        $this->fullName = $fullName;
        $this->nric = $nric;
        $this->dob = $dob;
        $this->gender = $gender;
        $this->phone = $phone;
        $this->address = $address;
        $this->registeredBy = $registeredBy;
        $this->patientId = $patientId;
    }

    // ---------------------------------------------------------------
    // Getters (encapsulation: controlled read access)
    // ---------------------------------------------------------------
    public function getPatientId(): ?int { return $this->patientId; }
    public function getFullName(): string { return $this->fullName; }
    public function getNric(): string { return $this->nric; }
    public function getDob(): string { return $this->dob; }
    public function getGender(): string { return $this->gender; }
    public function getPhone(): string { return $this->phone; }
    public function getAddress(): string { return $this->address; }

    /**
     * save()
     * -------------------------------------------------------------
     * Inserts a NEW patient into the database. Notice we encrypt
     * nric/phone/address RIGHT HERE, immediately before the SQL
     * query - this is the one and only place plain sensitive data
     * turns into ciphertext.
     * -------------------------------------------------------------
     */
    public function save(): bool
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare(
            'INSERT INTO patients
                (full_name, nric_encrypted, dob, gender, phone_encrypted, address_encrypted, registered_by)
             VALUES
                (:full_name, :nric, :dob, :gender, :phone, :address, :registered_by)'
        );

        $result = $stmt->execute([
            'full_name'     => $this->fullName,
            'nric'          => EncryptionHelper::encrypt($this->nric),
            'dob'           => $this->dob,
            'gender'        => $this->gender,
            'phone'         => EncryptionHelper::encrypt($this->phone),
            'address'       => EncryptionHelper::encrypt($this->address),
            'registered_by' => $this->registeredBy,
        ]);

        if ($result) {
            // Remember the auto-generated ID in case the caller needs it.
            $this->patientId = (int)$pdo->lastInsertId();
        }

        return $result;
    }

    /**
     * update()
     * -------------------------------------------------------------
     * Updates an EXISTING patient (this object must already have a
     * patientId, meaning it was loaded via findById() or search()).
     * -------------------------------------------------------------
     */
    public function update(): bool
    {
        if ($this->patientId === null) {
            // Defensive check: you cannot update a patient that was
            // never saved/loaded with a real ID.
            return false;
        }

        $pdo = Database::getConnection();

        $stmt = $pdo->prepare(
            'UPDATE patients SET
                full_name = :full_name,
                nric_encrypted = :nric,
                dob = :dob,
                gender = :gender,
                phone_encrypted = :phone,
                address_encrypted = :address
             WHERE patient_id = :id'
        );

        return $stmt->execute([
            'full_name' => $this->fullName,
            'nric'      => EncryptionHelper::encrypt($this->nric),
            'dob'       => $this->dob,
            'gender'    => $this->gender,
            'phone'     => EncryptionHelper::encrypt($this->phone),
            'address'   => EncryptionHelper::encrypt($this->address),
            'id'        => $this->patientId,
        ]);
    }

    /**
     * deleteById() - SOFT DELETE
     * -------------------------------------------------------------
     * We do NOT remove the row from the database. Instead we flip
     * is_deleted to 1, so the record is hidden from normal views but
     * still exists for audit/history purposes - a realistic choice
     * for hospital data, as agreed in Stage 2.
     * -------------------------------------------------------------
     */
    public static function deleteById(int $patientId): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE patients SET is_deleted = 1 WHERE patient_id = :id');
        return $stmt->execute(['id' => $patientId]);
    }

    /**
     * hydrate()
     * -------------------------------------------------------------
     * A private helper that turns ONE raw database row into a fully
     * decrypted Patient OBJECT. Every read method below (findAll,
     * findById, search) reuses this instead of repeating the same
     * decryption code three times.
     * -------------------------------------------------------------
     */
    private static function hydrate(array $row): Patient
    {
        $patient = new Patient(
            $row['full_name'],
            EncryptionHelper::decrypt($row['nric_encrypted']),
            $row['dob'],
            $row['gender'],
            EncryptionHelper::decrypt($row['phone_encrypted']),
            EncryptionHelper::decrypt($row['address_encrypted']),
            (int)$row['registered_by'],
            (int)$row['patient_id']
        );

        return $patient;
    }

    /**
     * findAll()
     * -------------------------------------------------------------
     * Returns every NON-deleted patient as an array of Patient
     * objects, ready to loop through in a view (e.g. patients/list.php).
     * -------------------------------------------------------------
     */
    public static function findAll(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query(
            'SELECT * FROM patients WHERE is_deleted = 0 ORDER BY created_at DESC'
        );

        $patients = [];
        foreach ($stmt->fetchAll() as $row) {
            $patients[] = self::hydrate($row);
        }

        return $patients;
    }

    /**
     * findById()
     * -------------------------------------------------------------
     * Returns a single Patient object (used for View/Edit forms) or
     * null if not found / already deleted.
     * -------------------------------------------------------------
     */
    public static function findById(int $patientId): ?Patient
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT * FROM patients WHERE patient_id = :id AND is_deleted = 0'
        );
        $stmt->execute(['id' => $patientId]);
        $row = $stmt->fetch();

        return $row ? self::hydrate($row) : null;
    }

    /**
     * search()
     * -------------------------------------------------------------
     * Searches patients by NAME only. This is why we deliberately
     * kept full_name in PLAIN TEXT in the database (see Stage 2/3
     * decision) - encrypted columns cannot be searched with a simple
     * SQL LIKE query, since the same plain text never produces the
     * same ciphertext twice (remember our random IV test earlier!).
     * -------------------------------------------------------------
     */
    public static function search(string $keyword): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT * FROM patients
             WHERE is_deleted = 0 AND full_name LIKE :keyword
             ORDER BY full_name ASC'
        );
        // The % wildcards mean "contains this text anywhere".
        $stmt->execute(['keyword' => '%' . $keyword . '%']);

        $patients = [];
        foreach ($stmt->fetchAll() as $row) {
            $patients[] = self::hydrate($row);
        }

        return $patients;
    }
}

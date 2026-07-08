<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/EncryptionHelper.php';

class MedicalRecord
{
    private ?int $recordId;
    private int $patientId;
    private int $doctorId;
    private string $diagnosis;   // plain in memory, encrypted in DB
    private string $treatment;   // plain in memory, encrypted in DB
    private string $visitDate;


    private ?string $patientName = null;
    private ?string $doctorName = null;

    public function __construct(
        int $patientId,
        int $doctorId,
        string $diagnosis,
        string $treatment,
        string $visitDate,
        ?int $recordId = null
    ) {
        $this->patientId = $patientId;
        $this->doctorId = $doctorId;
        $this->diagnosis = $diagnosis;
        $this->treatment = $treatment;
        $this->visitDate = $visitDate;
        $this->recordId = $recordId;
    }

    // Getters

    public function getRecordId(): ?int { return $this->recordId; }
    public function getPatientId(): int { return $this->patientId; }
    public function getDoctorId(): int { return $this->doctorId; }
    public function getDiagnosis(): string { return $this->diagnosis; }
    public function getTreatment(): string { return $this->treatment; }
    public function getVisitDate(): string { return $this->visitDate; }
    public function getPatientName(): string { return $this->patientName ?? ''; }
    public function getDoctorName(): string { return $this->doctorName ?? ''; }

    /**
     * save()
     
     */
    public function save(): bool
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare(
            'INSERT INTO medical_records
                (patient_id, doctor_id, diagnosis_encrypted, treatment_encrypted, visit_date)
             VALUES
                (:patient_id, :doctor_id, :diagnosis, :treatment, :visit_date)'
        );

        $result = $stmt->execute([
            'patient_id' => $this->patientId,
            'doctor_id'  => $this->doctorId,
            'diagnosis'  => EncryptionHelper::encrypt($this->diagnosis),
            'treatment'  => EncryptionHelper::encrypt($this->treatment),
            'visit_date' => $this->visitDate,
        ]);

        if ($result) {
            $this->recordId = (int)$pdo->lastInsertId();
        }

        return $result;
    }

    /**
     * update()
     
     */
    public function update(): bool
    {
        if ($this->recordId === null) {
            return false;
        }

        $pdo = Database::getConnection();

        $stmt = $pdo->prepare(
            'UPDATE medical_records SET
                diagnosis_encrypted = :diagnosis,
                treatment_encrypted = :treatment,
                visit_date = :visit_date
             WHERE record_id = :id'
        );

        return $stmt->execute([
            'diagnosis'  => EncryptionHelper::encrypt($this->diagnosis),
            'treatment'  => EncryptionHelper::encrypt($this->treatment),
            'visit_date' => $this->visitDate,
            'id'         => $this->recordId,
        ]);
    }

    /**
     * deleteById() - SOFT DELETE, same pattern as Patient::deleteById()
     */
    public static function deleteById(int $recordId): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE medical_records SET is_deleted = 1 WHERE record_id = :id');
        return $stmt->execute(['id' => $recordId]);
    }

    /**
     * hydrate()
    
     */
    private static function hydrate(array $row): MedicalRecord
    {
        $record = new MedicalRecord(
            (int)$row['patient_id'],
            (int)$row['doctor_id'],
            EncryptionHelper::decrypt($row['diagnosis_encrypted']),
            EncryptionHelper::decrypt($row['treatment_encrypted']),
            $row['visit_date'],
            (int)$row['record_id']
        );

        // If the query joined in extra name columns, attach them too.
        if (isset($row['patient_name'])) {
            $record->patientName = $row['patient_name'];
        }
        if (isset($row['doctor_name'])) {
            $record->doctorName = $row['doctor_name'];
        }

        return $record;
    }

    /**
     * findByPatientId()

     */
    public static function findByPatientId(int $patientId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT mr.*, u.full_name AS doctor_name
             FROM medical_records mr
             JOIN users u ON mr.doctor_id = u.user_id
             WHERE mr.patient_id = :patient_id AND mr.is_deleted = 0
             ORDER BY mr.visit_date DESC'
        );
        $stmt->execute(['patient_id' => $patientId]);

        $records = [];
        foreach ($stmt->fetchAll() as $row) {
            $records[] = self::hydrate($row);
        }

        return $records;
    }

    /**
     * findById()

     */
    public static function findById(int $recordId): ?MedicalRecord
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT * FROM medical_records WHERE record_id = :id AND is_deleted = 0'
        );
        $stmt->execute(['id' => $recordId]);
        $row = $stmt->fetch();

        return $row ? self::hydrate($row) : null;
    }

    /**
     * findAll()

     */
    public static function findAll(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query(
            'SELECT mr.*, p.full_name AS patient_name, u.full_name AS doctor_name
             FROM medical_records mr
             JOIN patients p ON mr.patient_id = p.patient_id
             JOIN users u ON mr.doctor_id = u.user_id
             WHERE mr.is_deleted = 0
             ORDER BY mr.visit_date DESC'
        );

        $records = [];
        foreach ($stmt->fetchAll() as $row) {
            $records[] = self::hydrate($row);
        }

        return $records;
    }
}

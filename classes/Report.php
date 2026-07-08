<?php


require_once __DIR__ . '/Patient.php';
require_once __DIR__ . '/MedicalRecord.php';

class Report
{
    /**
     * generatePatientListReport()

     */
    public static function generatePatientListReport(): array
    {
        return Patient::findAll();
    }

    /**

     */
    public static function generateMedicalRecordsReport(): array
    {
        return MedicalRecord::findAll();
    }
}

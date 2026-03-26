<?php

namespace App\Imports;

use App\Models\RegistrationCode;
use App\Services\StudentRegistrationService;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    /**
     * @var StudentRegistrationService
     */
    protected $registrationService;

    /**
     * Import counter
     */
    protected $importCount = 0;

    public function __construct()
    {
        $this->registrationService = new StudentRegistrationService();
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $firstName = trim($row['fname'] ?? $row['first_name'] ?? $row['firstname'] ?? '');
        $lastName = trim($row['lname'] ?? $row['last_name'] ?? $row['lastname'] ?? '');
        $middleName = trim($row['mname'] ?? $row['middle_name'] ?? $row['middlename'] ?? null);

        if (empty($firstName) || empty($lastName)) {
            return null;
        }

        // Generate credentials using the service
        $credentials = $this->registrationService->generateCredentials($firstName, $lastName);

        $this->importCount++;

        return new RegistrationCode([
            'code' => $credentials['code'],
            'first_name' => $firstName,
            'last_name' => $lastName,
            'middle_name' => $middleName,
            'username' => $credentials['username'],
            'default_password' => $credentials['default_password'],
            'student_code' => $credentials['student_code'],
            'used' => false,
        ]);
    }

    /**
     * Validation rules
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'fname' => 'nullable|string|max:255',
            'lname' => 'nullable|string|max:255',
            'mname' => 'nullable|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'firstname' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'middlename' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get the number of imported records
     *
     * @return int
     */
    public function getImportCount(): int
    {
        return $this->importCount;
    }
}

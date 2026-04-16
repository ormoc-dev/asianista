<?php

namespace App\Services;

use App\Models\RegistrationCode;
use Illuminate\Support\Str;

class StudentRegistrationService
{
    /**
     * Generate unique credentials for a student
     *
     * @param string $firstName
     * @param string $lastName
     * @return array
     */
    public function generateCredentials(string $firstName, string $lastName): array
    {
        // Clean names for username
        $cleanFirst = $this->cleanNameForUsername($firstName);
        $cleanLast = $this->cleanNameForUsername($lastName);

        // Generate unique username
        $username = $this->generateUniqueUsername($cleanFirst, $cleanLast);

        // Generate default password
        $defaultPassword = $this->generateDefaultPassword();

        // Generate student code for login
        $studentCode = $this->generateUniqueStudentCode();

        // Generate internal code
        $code = $this->generateInternalCode();

        return [
            'username' => $username,
            'default_password' => $defaultPassword,
            'student_code' => $studentCode,
            'code' => $code,
        ];
    }

    /**
     * Clean name for use in username
     *
     * @param string $name
     * @return string
     */
    protected function cleanNameForUsername(string $name): string
    {
        // Remove special characters and spaces
        $cleaned = preg_replace('/[^a-zA-Z0-9]/', '', $name);
        return strtolower($cleaned);
    }

    /**
     * Generate unique username
     *
     * @param string $firstName
     * @param string $lastName
     * @return string
     */
    protected function generateUniqueUsername(string $firstName, string $lastName): string
    {
        $baseUsername = $firstName . '.' . $lastName;
        $username = $baseUsername;
        $counter = 1;

        // Check for uniqueness
        while (RegistrationCode::where('username', $username)->exists()) {
            $username = $baseUsername . '.' . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Generate default password
     *
     * @param int $length
     * @return string
     */
    protected function generateDefaultPassword(int $length = 8): string
    {
        // Generate a readable password with letters and numbers
        $letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';

        $password = '';

        // Add 6 random letters
        for ($i = 0; $i < $length - 2; $i++) {
            $password .= $letters[random_int(0, strlen($letters) - 1)];
        }

        // Add 2 random numbers
        for ($i = 0; $i < 2; $i++) {
            $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        }

        // Shuffle the password
        return str_shuffle($password);
    }

    /**
     * Generate unique student code
     *
     * @return string
     */
    protected function generateUniqueStudentCode(): string
    {
        do {
            // Format: STU-XXXXXX (6 alphanumeric characters)
            $code = 'STU-' . strtoupper(Str::random(6));
        } while (RegistrationCode::where('student_code', $code)->exists());

        return $code;
    }

    /**
     * Generate internal code
     *
     * @return string
     */
    protected function generateInternalCode(): string
    {
        do {
            $code = strtoupper(bin2hex(random_bytes(3))); // 6 chars
        } while (RegistrationCode::where('code', $code)->exists());

        return $code;
    }

    /**
     * Regenerate credentials for a specific registration code
     *
     * @param RegistrationCode $registrationCode
     * @return array
     */
    public function regenerateCredentials(RegistrationCode $registrationCode): array
    {
        $credentials = $this->generateCredentials(
            $registrationCode->first_name,
            $registrationCode->last_name
        );

        $registrationCode->update([
            'code' => $credentials['code'],
            'username' => $credentials['username'],
            'default_password' => $credentials['default_password'],
            'student_code' => $credentials['student_code'],
        ]);

        return $credentials;
    }

    /**
     * Create Excel template for download
     *
     * @return string
     */
    public function createExcelTemplate(): string
    {
        $headers = ['FNAME', 'LNAME', 'MNAME'];

        $tempFile = tempnam(sys_get_temp_dir(), 'student_template_');
        $filePath = $tempFile . '.csv';

        $file = fopen($filePath, 'w');

        // Add UTF-8 BOM for Excel compatibility
        fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Write headers
        fputcsv($file, $headers);

        // Add sample rows (students choose grade & section when they register online)
        fputcsv($file, ['John', 'Doe', 'Michael']);
        fputcsv($file, ['Jane', 'Smith', '']);
        fputcsv($file, ['Robert', 'Johnson', 'James']);

        fclose($file);

        return $filePath;
    }
}

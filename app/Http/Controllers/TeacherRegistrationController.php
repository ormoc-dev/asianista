<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\RegistrationCode;
use App\Imports\StudentsImport;
use App\Services\StudentRegistrationService;
use Maatwebsite\Excel\Facades\Excel;

class TeacherRegistrationController extends Controller
{
    /**
     * @var StudentRegistrationService
     */
    protected $registrationService;

    public function __construct()
    {
        $this->registrationService = new StudentRegistrationService();
    }

    /**
     * Show all students and pending registrations
     */
    public function index()
    {
        $students = User::where('role', 'student')
            ->where('status', 'pending')
            ->with(['grade', 'section'])
            ->orderBy('created_at', 'desc')
            ->get();
        $pendingRegistrations = RegistrationCode::where('used', false)
            ->whereNotNull('student_code')
            ->with(['grade', 'section'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('teacher.registration.index', compact('students', 'pendingRegistrations'));
    }

    /**
     * Generate a single registration code (legacy method)
     */
    public function generateCode()
    {
        $code = strtoupper(bin2hex(random_bytes(3))); // e.g., 6 chars
        RegistrationCode::create(['code' => $code]);
        return back()->with('success', "New code generated: $code");
    }

    /**
     * Handle Excel file upload for bulk student registration
     */
    public function uploadExcel(Request $request)
    {
        $request->validate([
            'student_file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $import = new StudentsImport();
            Excel::import($import, $request->file('student_file'));

            $count = $import->getImportCount();

            if ($count > 0) {
                return back()->with('success', "Successfully imported {$count} student(s).");
            } else {
                return back()->with('warning', 'No valid student records found in the file.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template for student registration
     */
    public function downloadTemplate()
    {
        $filePath = $this->registrationService->createExcelTemplate();

        return response()->download($filePath, 'student_registration_template.csv', [
            'Content-Type' => 'text/csv',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Regenerate credentials for a specific registration code
     */
    public function regenerateCredentials($id)
    {
        $registrationCode = RegistrationCode::findOrFail($id);

        if ($registrationCode->used) {
            return back()->with('error', 'Cannot regenerate credentials for already registered student.');
        }

        $credentials = $this->registrationService->regenerateCredentials($registrationCode);

        return back()->with('success', "Credentials regenerated successfully. New Student Code: {$credentials['student_code']}");
    }

    /**
     * Delete a pending registration
     */
    public function destroyPending($id)
    {
        $registrationCode = RegistrationCode::findOrFail($id);

        if ($registrationCode->used) {
            return back()->with('error', 'Cannot delete already registered student.');
        }

        $registrationCode->delete();

        return back()->with('success', 'Pending registration deleted successfully.');
    }

    public function approveStudent($id)
    {
        $student = User::where('role', 'student')->findOrFail($id);

        if ($student->status !== 'pending') {
            return back()->with('warning', "{$student->name} is already {$student->status}.");
        }

        $student->update(['status' => 'approved']);

        return back()->with('success', "{$student->name} has been approved.");
    }

    public function bulkApproveStudents(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'integer|exists:users,id',
        ]);

        $updated = User::whereIn('id', $validated['student_ids'])
            ->where('role', 'student')
            ->where('status', 'pending')
            ->update(['status' => 'approved']);

        if ($updated === 0) {
            return back()->with('warning', 'No students were approved.');
        }

        return back()->with('success', "{$updated} student(s) approved successfully.");
    }
}

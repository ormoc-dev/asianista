<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GradeController extends Controller
{
    /**
     * Get sections for a specific grade.
     */
    public function getSections($gradeId)
    {
        try {
            Log::info('Loading sections for grade ID: ' . $gradeId);
            
            // Find the grade
            $grade = Grade::find($gradeId);
            if (!$grade) {
                Log::error('Grade not found: ' . $gradeId);
                return response()->json(['error' => 'Grade not found'], 404);
            }
            
            // Get sections using the relationship
            $sections = $grade->sections()->orderBy('name')->get();
            Log::info('Found ' . $sections->count() . ' sections for grade ' . $gradeId);
            
            // Also try direct query to debug
            $directSections = Section::where('grade_id', $gradeId)->get();
            Log::info('Direct query found ' . $directSections->count() . ' sections');
            
            return response()->json($sections);
        } catch (\Exception $e) {
            Log::error('Error loading sections: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

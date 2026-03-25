<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Grade;
use App\Models\Section;

class GradeSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $grades = [
            'Grade 7',
            'Grade 8',
            'Grade 9',
            'Grade 10',
            'Grade 11',
            'Grade 12',
        ];

        foreach ($grades as $gradeName) {
            $grade = Grade::create(['name' => $gradeName]);
            
            // Create sections A and B for each grade
            Section::create([
                'grade_id' => $grade->id,
                'name' => 'A'
            ]);
            
            Section::create([
                'grade_id' => $grade->id,
                'name' => 'B'
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class GradeSubjectController extends Controller
{
    public function attachSubject(Request $request, $gradeNumber)
    {
        $students = Student::where('grade_level', $gradeNumber)->get();
        foreach ($students as $student) {
            if (!$student->subjects->contains($request->subject_id)) {
                 $student->subjects()->attach($request->subject_id);
                $student->save();

            }
        }
        return response()->json(['message' => 'Subjects attached successfully']);

    }

    public function detachSubject(Request $request, $gradeNumber)
    {
        $students = Student::where('grade_level', $gradeNumber)->get();
        foreach ($students as $student) {$student->subjects()->detach($request->subject_id);}
        return response()->json(['message' => 'Subjects detached successfully']);
    }

}

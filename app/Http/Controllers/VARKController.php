<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\TimeTable;
use App\Models\VARK;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VARKController extends Controller
{
    public function store(Request $request)
    {
        $key = $this->getGreatestKey($request->all());
        $request->merge(['user_id' => Auth::id(), 'result' => $key]);
        VARK::create($request->all());
        $user = Auth::user();
        $user->isFirstTimeLogin = false;
        $user->save();
        return response()->json(['success' => 'Test Results created successfully'], 200);
    }

    private function getGreatestKey($data)
    {
        $greatest_value = 0;
        $key = null;

        foreach ($data as $datumKey => $datum) {
            if ($datum > $greatest_value) {
                $greatest_value = $datum;
                $key = $datumKey;
            }
        }

        return $key;


    }

    public function getCountedVarkResults($teacher_id)
    {
        // Fetch all class IDs taught by the teacher
        $teachedClasses = TimeTable::select('class_id')
            ->where('teacher_id', $teacher_id)
            ->pluck('class_id')
            ->toArray();

        $classes = [];

        // Loop through each class ID
        foreach ($teachedClasses as $classId) {
            // Fetch all student user IDs in the current class
            $studentsUsersIdsInClasses = Student::where('class_id', $classId)
                ->pluck('user_id')
                ->toArray();

            // Fetch VARK results for students in the current class
            $varkStudents = VARK::whereIn('user_id', $studentsUsersIdsInClasses)->get();

            // Count VARK results
            $classes[] = [
                'id' =>$classId,
                'v' => $varkStudents->where('result', 'v')->count(),
                'a' => $varkStudents->where('result', 'a')->count(),
                'r' => $varkStudents->where('result', 'r')->count(),
                'k' => $varkStudents->where('result', 'k')->count(),
            ];
        }

        return $classes;
    }

}

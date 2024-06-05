<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\VARK;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VARKController extends Controller
{
    public function store(Request $request)
    {
       $key =  $this->getGreatestKey($request->all());
        $request->merge(['user_id' => Auth::id(),'result' => $key]);
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
            if($datum > $greatest_value)
            {
                $greatest_value = $datum;
                $key = $datumKey;
            }
        }

        return $key;


    }

    public function getCountedVarkResults($teacher_id)
    {
        $teachedClasses = ClassRoom::select('class_id')->where('teacher_id',$teacher_id)->get()->toArray();
        $studentsIdsInClasses = Student::whereIn($teachedClasses)->pluck('id')->toArray();
        $varkStudents = VARK::whereIn('student_id',$studentsIdsInClasses);
    }
}

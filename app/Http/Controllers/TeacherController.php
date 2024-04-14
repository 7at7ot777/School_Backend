<?php

namespace App\Http\Controllers;

use App\Models\Employee;

use App\Models\TimeTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    public static $rules = [
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:255',
        'address' => 'nullable|string|max:255',
        'password' => 'required|string|max:255',
        'email' => 'required|email|unique:users|max:255',
        'department_id' => 'required|exists:departments,id',
        'basic_salary' => 'required|integer',
        'subject_id' => 'required|exists:subjects,id',
    ];

    public static $errorMessages = [
        'name.required' => 'The name field is required',
        'name.string' => 'The name field must be a string',
        'name.max' => 'The name field must not exceed :max characters',

        'phone.string' => 'The phone field must be a string',
        'phone.max' => 'The phone field must not exceed :max characters',

        'address.string' => 'The address field must be a string',
        'address.max' => 'The address field must not exceed :max characters',

        'password.required' => 'The password field is required',
        'password.string' => 'The password field must be a string',
        'password.max' => 'The password field must not exceed :max characters',

        'email.required' => 'The email field is required',
        'email.email' => 'The email field must be a valid email address',
        'email.unique' => 'This email address is already in use',
        'email.max' => 'The email field must not exceed :max characters',

        'department_id.required' => 'The department number field is required',
        'department_id.exists' => 'Invalid department selected',

        'basic_salary.required' => 'The basic salary field is required',
        'basic_salary.integer' => 'The basic salary field must be an integer',

        'subject_id.required' => 'The subject ID field is required',
        'subject_id.exists' => 'Invalid subject selected',

    ];

    public function index()
    {
        $teachers = Employee::with('subject:id,name', 'user:id,email,name,phone,status,avatar_url')
            ->where('role', 'teacher')
            ->whereHas('user', function ($query) {
            $query->where('status', 1);
        })->get();
        $formattedTeachers = $this->formatTeachers($teachers);
        return response()->json($formattedTeachers, 200);
    }

    private function formatTeachers($data)
    {
        $resultArray = [];
        foreach ($data as $item) {
            $subjects = [];
            $objSub = $item->subject;

            foreach ($objSub as $subject ){

                $subjects[] = [
                    'id' => $subject->id,
                    'name' => $subject->name,
                ];

//
            }
            $resultArray[] = [
                'teacher_id' => $item['id'],
                'id' => $item['user']['id'],
                'avatarUrl' => $item['user']['avatar_url'] ?? '',
                'name' => $item['user']['name'],
                'email' => $item['user']['email'],
                'status' => $item['user']['status'] == 0 ? false : true,
                'subject' => $subjects,
            ];

        }
        return $resultArray;
    }


    public function show($id)
    {
        $teacher = Employee::with('subject')
            ->where('role', 'teacher')
            ->where('id', $id)
            ->first();

        if (!$teacher) {
            return response()->json(['error' => 'Teacher not found'], 404);
        }
        $formattedTeachers = $this->formatTeachers([$teacher]);


        return response()->json(['data' => $formattedTeachers], 200);
    }

    public function getTeachersSubjects($teacher_id)
    {
        $teacher = Employee::where('role','teacher')->find($teacher_id);

        if ($teacher) {
            $subjectsTaughtByTeacher = $teacher->subject->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    // Add more attributes if needed
                ];
            });
            return response()->json($subjectsTaughtByTeacher, 200);

        }
        return response()->json(['error'=>'Teacher Not Found'], 404);

    }

    public function dashboard()
    {
        //TODO: You have to return this code if it doesn't work with muhannad
//        $employee = Employee::where('id',Auth::id())->first();
//        $numOfSubjects = $employee->subject()->count();
        $employee = Employee::where('id', Auth::id())->first(); // Use firstOrFail to throw an exception if no employee found

        $employeeWithSubjects = $employee->load('subject');

        $numOfSubjects = $employeeWithSubjects->subject->count() ?? 0; // Use eager loaded subjects

        $numOfPeriodsToday = TimeTable::where('teacher_id',$employee->id)->where('day',date('l'))->count() ?? 0;

        return response()->json(['numOfSubjects'=>$numOfSubjects,'numOfPeriodsToday' => $numOfPeriodsToday]);

    }

    
}
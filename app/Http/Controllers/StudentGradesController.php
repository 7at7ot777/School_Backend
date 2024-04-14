<?php

namespace App\Http\Controllers;

use App\Models\StudentGrades;
use Illuminate\Http\Request;

class StudentGradesController extends Controller
{
    public function index($subjectId)
    {
        $models = StudentGrades::with('student.user','subject','student.classroom')->where('subject_id',$subjectId)->get();
//        return $models;
        $formatedStudentGrades = $this->formatIndexData($models);
        return response()->json($formatedStudentGrades);
    }

    private function formatIndexData($data)
    {
        $extractedData = [];
        foreach ($data as $grade) {
            $student = $grade['student'];
            $user = $student['user'];
            $classroom = $student['classroom'];
            $subject = $grade['subject'];

            $extractedData[] = [
                'grades' =>
                    [
                        'id' => $grade['id'],
                        'midterm' => $grade['midterm'],
                        'behavior' => $grade['behavior'],
                        'final' => $grade['final'],
                        'attendance' => $grade['attendance'],
                        'total' => $grade['total']
                    ],
                'student' =>[
                    'id' => $user['id'],
                    'student_id' => $grade['student_id'],
                    'name' => $user['name'],
                    'email' => $user['email']
                ],
                'subject' =>[
                    'id' => $subject['id'],
                    'name' => $subject['name']
                ],
                'classroom' =>[
                    'id' => $classroom['id'],
                    'grade' => $classroom['grade'],
                    'class_number' => $classroom['class_number']
                ],
            ];
    }
        return $extractedData;

    }

    public function store(Request $request)
    {
        StudentGrades::create($request->all());
        return response()->json(['success'=>'Grade is added successfully']);
    }

    public function show($subjectId,$studentId)
    {
        $model = StudentGrades::with('student.user','subject')
            ->where('subject_id',$subjectId)
            ->where('student_id',$studentId)->first();
        $formatedStudentGrades = $this->formatData([$model]);
        return response()->json($formatedStudentGrades);
    }

    public function update(Request $request, $id)
    {
        $model = StudentGrades::findOrFail($id);
        $model->update($request->all());
        return response()->json(['success'=>'Grade is updated successfully']);
    }

    public function destroy($id)
    {
        StudentGrades::findOrFail($id)->delete();
        return response()->json(['success'=>'Grade is deleted successfully']);
    }

    public function formatData($data)
    {
        $extractedData = [];
        foreach ($data as $grade) {
           $student = $grade['student'];
            $user = $student['user'];
            $subject = $grade['subject'];

            $extractedData[] = [
                'grades' =>
                    [
                'midterm' => $grade['midterm'],
                'behavior' => $grade['behavior'],
                'final' => $grade['final'],
                'attendance' => $grade['attendance'],
                'total' => $grade['total']
                    ],
                'user_id' => $user['id'],
                'student' =>[
                'student_id' => $grade['student_id'],
                'name' => $user['name'],
                'email' => $user['email']
                    ],
                'subject' =>[
                'id' => $subject['id'],
                'name' => $subject['name']
                    ],
            ];
        }
        return $extractedData;
    }

    public function getStudentGrade($studentId)
    {
        $model = StudentGrades::with('subject')
            ->where('student_id',$studentId)->get();
        $formattedData = $model->map(function ($grade) {
            return [
                'subject_name' => $grade['subject']['name'],  // Include subject name
                'student_id' => $grade['student_id'],
                'subject_id' => $grade['subject_id'],
                'midterm' => $grade['midterm'],
                'final' => $grade['final'],
                'attendance' => $grade['attendance'],
                'behavior' => $grade['behavior'],
                'total' => $grade['total'] ,  // Calculate total here
            ];
        });
        return $formattedData;
    }
}

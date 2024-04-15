<?php

namespace App\Http\Controllers;

use App\Models\Student;
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

    public function studentsWithNoGrades($subject_id)
    {
        $existingGradesStudentIds = StudentGrades::where('subject_id',$subject_id)
            ->pluck('student_id')
            ->toArray();
         $allStudentIds  = Student::with('subjects')->whereHas('subjects',function ($query)use($subject_id){
            $query->where('subject_id',$subject_id);
        })
          ->pluck('id')
          ->toArray();

        $missingStudentIds = array_diff($allStudentIds, $existingGradesStudentIds);
        $missingStudentIds = array_values($missingStudentIds);

         $students = Student::with('user','classroom')->whereIn('id', $missingStudentIds)->get()->toArray();
        $transformedData = array_map(function ($item) {
            return [
                'id' => isset($item['user']['id']) ? $item['user']['id'] : null,
                'student_id' => isset($item['id']) ? $item['id'] : null,
                'name' => isset($item['user']['name']) ? $item['user']['name'] : null,
                'email' => isset($item['user']['email']) ? $item['user']['email'] : null,
                'classroom_id' => isset($item['classroom']['id']) ? $item['classroom']['id'] : null,
                'grade' => isset($item['classroom']['grade']) ? $item['classroom']['grade'] : null,
                'class_number' => isset($item['classroom']['class_number']) ? $item['classroom']['class_number'] : null,
            ];
        }, $students);

        return $transformedData;


    }
}

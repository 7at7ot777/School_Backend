<?php

namespace App\Http\Controllers;

use App\Imports\ImportSubject;
use App\Models\Student;
use App\Models\Subject;
use App\Http\Controllers\Controller;
use App\Models\TimeTable;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subjects = Subject::with('teachers.user')->get();
//        return $subjects;
        $formattedData = $this->formatDate($subjects);
        return response()->json($formattedData, 200);
    }

    private function formatDate($data)
    {
        $resultArray = [];

        foreach ($data as $subject) {
            $mainAdmin = $subject->teachers->first();

            $resultArray[] = [
                'id' => $subject->id,
                'name' => $subject->name,
              //  'numOfAdmins' => $subject->employees->where('role', 'admin')->where('user.status',1)->count() ,
                'numOfTeachers' => $subject->teachers->where('user.status',1)->count(),
                'mainAdmin' => [
                    'id' => $mainAdmin ? $mainAdmin->user->id : '',
                    'name' => $mainAdmin ? $mainAdmin->user->name : '',
                    'avatarUrl' => $mainAdmin ? $mainAdmin->user->avatar_url : '',
                ],
            ];
        }
        return $resultArray;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $subject = Subject::create($request->all());

        return response()->json(['message' => 'stored successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        return response()->json($subject, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $subject->update($request->all());

        return response()->json(['message' => 'Updated successfully'], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        if(!$subject)
        {
            return response()->json(['message' => 'Subject Not Found'], 404);
        }
        $subject->delete();

        return response()->json(['message' => 'Subject Deleted Successfully'], 200);
    }

    public function DownloadSubjectTemplate()
    {
        $filePath = public_path("storage/uploads/importSubject.xlsx");
        $filename = 'importSubject.xlsx';
        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }

    public function importSubject(Request $request){
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $importSubject = new ImportSubject();
            Excel::import($importSubject, $file);
            return response()->json(['success' =>  $importSubject->counter. ' Subjects imported successfully']);
        }
        return response()->json(['error'=>'No File Provided'],401);
    }

    public function getSubjectStudents($subjectId)
    {
        $students = Student::with('user')->whereHas('subjects', function ($query) use ($subjectId) {
            $query->where('subjects.id', $subjectId);
        })->get();

        $mappedData = collect($students)->map(function ($item) {
            return [
                'student_id' => $item['id'],
                'id' => $item['user']['id'],
                'name' => $item['user']['name'],
                'email' => $item['user']['email'],
            ];
        });
        return $mappedData;
    }

    public function getClassSubjects($class_id)
    {
        $subjectIds = TimeTable::where('class_id', $class_id)->pluck('subject_id')->toArray();
        return Subject::whereIn('id',$subjectIds)->get();
    }
}

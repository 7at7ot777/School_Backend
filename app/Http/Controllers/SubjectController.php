<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subjects = Subject::with('teachers.user')->get();
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
                'NumOfTeachers' => $subject->teachers->where('user.status',1)->count(),
                'mainAdmin' => [
                    'id' => $mainAdmin ? $mainAdmin->id : '',
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
        $subject->delete();

        return response()->json(null, 204);
    }
}

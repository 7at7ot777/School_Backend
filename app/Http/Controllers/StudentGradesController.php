<?php

namespace App\Http\Controllers;

use App\Models\StudentGrades;
use Illuminate\Http\Request;

class StudentGradesController extends Controller
{
    public function getAllGrades($subjectId)
    {
        $models = StudentGrades::with('student')->where('subject_id',$subjectId)->get();
        return response()->json($models);
    }

    public function store(Request $request)
    {
        $model = StudentGrades::create($request->all());
        return response()->json($model, 201);
    }

    public function show($id)
    {
        $model = StudentGrades::findOrFail($id);
        return response()->json($model);
    }

    public function update(Request $request, $id)
    {
        $model = StudentGrades::findOrFail($id);
        $model->update($request->all());
        return response()->json($model, 200);
    }

    public function destroy($id)
    {
        StudentGrades::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\GradeFees;
use Illuminate\Http\Request;


class GradeFeesController extends Controller
{
    public function index()
    {
        return GradeFees::all();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'amount' => 'required|numeric',
            'grade' => 'required|string|max:255',
        ]);

        $gradeFee = GradeFees::create($validatedData);

        return response()->json($gradeFee, 201);
    }

    public function show($id)
    {
        $gradeFee = GradeFees::findOrFail($id);

        return response()->json($gradeFee);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'amount' => 'sometimes|required|numeric',
            'grade' => 'sometimes|required|string|max:255',
        ]);

        $gradeFee = GradeFees::findOrFail($id);
        $gradeFee->update($validatedData);

        return response()->json($gradeFee);
    }

    public function destroy($id)
    {
        $gradeFee = GradeFees::findOrFail($id);
        $gradeFee->delete();

        return response()->json(null, 204);
    }


}

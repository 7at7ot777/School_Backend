<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\StudentAttendance;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentAttendance $studentAttendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudentAttendance $studentAttendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudentAttendance $studentAttendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentAttendance $studentAttendance)
    {
        //
    }

    public function recordAbsence(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
        ]);

        $studentId = $request->input('student_id');
        $date = Carbon::parse($request->input('date'));

        $attendance = StudentAttendance::recordAbsence($studentId, $date);

        return response()->json(['message' => 'Absence recorded successfully', 'data' => $attendance], 201);
    }



    
    public function getAbsenceDays($studentId)
    {
        // احسب الفترة من تاريخ اليوم حتى اليوم السابق
        $today = Carbon::now();
        $oneDayAgo = Carbon::now()->subDay();

        // ابحث عن جميع سجلات الحضور للطالب في الفترة المحددة
        $absentRecords = StudentAttendance::where('student_id', $studentId)
            ->whereBetween('date', [$oneDayAgo, $today])
            ->where('status', 0) // 0 يعني غياب
            ->get();

        // عدد الأيام التي غابها الطالب
        $absenceDaysCount = $absentRecords->count();

        return $absenceDaysCount;
    }
}

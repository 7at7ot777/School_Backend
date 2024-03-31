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

    public function recordAttendance(Request $request)
    {
        // التحقق من وجود المعلومات المطلوبة في الطلب
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date_format:Y-m-d',
            'status' => 'required|boolean',
        ]);

        try {
            // إنشاء سجل حضور جديد
            $attendance = new StudentAttendance([
                'student_id' => $request->student_id,
                'date' => $request->date,
                'day' => date('d', strtotime($request->date)),
                'month' => date('m', strtotime($request->date)),
                'year' => date('Y', strtotime($request->date)),
                'status' => $request->status,
            ]);

            // حفظ السجل في قاعدة البيانات
            $attendance->save();

            return response()->json(['message' => 'Attendance recorded successfully'], 201);
        } catch (\Exception $e) {
            // التعامل مع الخطأ إذا حدث
            return response()->json(['error' => 'Failed to record attendance', 'message' => $e->getMessage()], 500);
        }
    }



    public function calculateAbsenceDays($studentId)
    {
        // العثور على سجلات الغياب للطالب
        $absenceRecords = StudentAttendance::where('student_id', $studentId)
            ->where('status', 0) // 0 يعني غياب
            ->get();
    
        // حساب عدد الأيام التي غاب فيها الطالب
        $absenceDays = $absenceRecords->count();
    
        // إرجاع النتيجة
        return $absenceDays;
    }
}

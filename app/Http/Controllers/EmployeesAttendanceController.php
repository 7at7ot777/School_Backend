<?php

namespace App\Http\Controllers;

use App\Models\EmployeesAttendance;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeesAttendanceController extends Controller
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
    public function show(EmployeesAttendance $employeesAttendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmployeesAttendance $employeesAttendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmployeesAttendance $employeesAttendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeesAttendance $employeesAttendance)
    {
        //
    }

    public function recordAttendance(Request $request)
    {
        // التحقق من وجود المعلومات المطلوبة في الطلب
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date_format:Y-m-d',
            'status' => 'required|boolean',
        ]);

        try {
            // إنشاء سجل حضور جديد
            $attendance = new EmployeesAttendance([
                'user_id' => $request->user_id,
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

    public function calculateAbsenceDays($employeeId)
    {
        // العثور على سجلات الغياب للموظف
        $absenceRecords = EmployeesAttendance::where('user_id', $employeeId)
            ->where('status', 0) // 0 يعني غياب
            ->get();

        // حساب عدد الأيام التي غاب فيها الموظف
        $absenceDays = $absenceRecords->count();

        // إرجاع النتيجة
        return $absenceDays;
    }
}

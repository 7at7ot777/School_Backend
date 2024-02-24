<?php

namespace App\Http\Controllers;

use App\Imports\ImportStudent;
use App\Models\Student;
use App\Models\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public static $rules = [
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:255',
        'address' => 'nullable|string|max:255',
        'password' => 'nullable|string|max:255',
        'email' => 'required|email|unique:users|max:255',
        'grade_level' => 'required|integer',
        'parent_id_one' => 'required|integer',
        'parent_id_two' => 'required|integer',
        'class_id' => 'required|integer',
        'semester' => 'required|integer|in:1,2,3',
    ];

    public static $errorMessages = [
        'name.required' => 'The name field is required.',
        'name.string' => 'The name field must be a string.',
        'name.max' => 'The name field must not exceed 255 characters.',

        'phone.string' => 'The phone field must be a string.',
        'phone.max' => 'The phone field must not exceed 255 characters.',

        'address.string' => 'The address field must be a string.',
        'address.max' => 'The address field must not exceed 255 characters.',

        'password.required' => 'The password field is required.',
        'password.string' => 'The password field must be a string.',
        'password.max' => 'The password field must not exceed 255 characters.',

        'email.required' => 'The email field is required.',
        'email.email' => 'The email must be a valid email address.',
        'email.unique' => 'The specified email address is already taken.',
        'email.max' => 'The email field must not exceed 255 characters.',

        'grade_level.required' => 'The grade level field is required.',
        'grade_level.integer' => 'The grade level must be an integer.',

        'parent_id_one.required' => 'The parent ID one field is required.',
        'parent_id_one.integer' => 'The parent ID one must be an integer.',

        'parent_id_two.required' => 'The parent ID two field is required.',
        'parent_id_two.integer' => 'The parent ID two must be an integer.',

        'class_id.required' => 'The class ID field is required.',
        'class_id.integer' => 'The class ID must be an integer.',

        'semester.required' => 'The semester field is required.',
        'semester.integer' => 'The semester must be an integer.',
        'semester.in' => 'Invalid value for the semester field. Allowed values are 1, 2, 3.',
    ];
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // استرجاع جميع الطلاب من قاعدة البيانات
        $students = Student::all();

        // التأكد مما إذا كان هناك طلاب متاحون
        if ($students->isEmpty()) {
            return response()->json(['message' => 'No students found'], 404);
        }

        // تنسيق بيانات الطلاب
        $formattedStudents = $students->map(function ($student) {
            return [
                'id' => $student->id,
                'name' => $student->user->name,
                'grade_level' => $student->grade_level,
                'is_active' => $student->is_active,
                'parent_id_one' => $student->parent_id_one,
                'parent_id_two' => $student->parent_id_two,
                'class_id' => $student->class_id,
                'semester' => $student->semester,
                // يمكنك إضافة المزيد من البيانات حسب الحاجة
            ];
        });

        // إرجاع بيانات الطلاب كاستجابة JSON
        return response()->json($formattedStudents, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), self::$rules, self::$errorMessages);
        if ($validator->fails()) {
            return $validator->errors();
        }
//        try {
            // Create a new user instance
            $user = User::create([
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'password' => bcrypt('welcome'),
                'email' => $request->input('email'),
                'user_type' => 'student'
            ]);

            // Create a new student instance
            $student = new Student([
                'user_id' => $user->id,
                'grade_level' => $request->input('grade_level'),
                'parent_id_one' => $request->input('parent_id_one'),
                'parent_id_two' => $request->input('parent_id_two'),
                'class_id' => $request->input('class_id'),
                'semester' => $request->input('semester'),
            ]);

            // Save the student to the database
            $student->save();

            // Save the user
            $user->save();

            // Return a success response
            return response()->json(['message' => 'Student created successfully'], 201);
//        } catch (\Exception $e) {
//            // Return an error response if an exception occurred
//            return response()->json(['error' => 'Failed to create student','ERROR'=>$e], 500);
//        }
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
    public function show($id)
    {
        $user = User::find($id);
        $user->load(['student.father','student.mother','student.classroom','payments']);
//        return $user;
        $formatedUser = $this->formatStudent($user);

        return response()->json($formatedUser);
    }

    private function formatStudent(User $user){

        $result = [
            'name' => $user->name ?? null,
            'phone' => $user->phone ?? null,
            'address' => $user->address ?? null,
            'status' => $user->status ?? null,
            'email' => $user->email ?? null,
            'avatarUrl' => $user->avatar_url ?? null,
            'userType' => $user->user_type ?? null,
            'grade' => $user->student->grade_level ?? null,
            'class' => [
                'grade' => $user->student->classroom->grade ?? null,
                'class_number' => $user->student->classroom->class_number ?? null,
            ],
            'father' => [
                'id' => $user->student->father->name ?? null,
                'name' => $user->student->father->name ?? null,
                'phone' => $user->student->father->phone ?? null,
                'address' => $user->student->father->address ?? null,
                'status' => $user->student->father->status ?? null,
                'email' => $user->student->father->email ?? null,
                'avatarUrl' => $user->student->father->avatar_url ?? null,
                'userType' => $user->student->father->user_type ?? null,
            ],
            'mother' => [
                'id' => $user->student->mother->name ?? null,
                'name' => $user->student->mother->name ?? null,
                'phone' => $user->student->mother->phone ?? null,
                'address' => $user->student->mother->address ?? null,
                'status' => $user->student->mother->status ?? null,
                'email' => $user->student->mother->email ?? null,
                'avatarUrl' => $user->student->mother->avatar_url ?? null,
                'userType' => $user->student->mother->user_type ?? null,
            ],
            'payments' => []
        ];

        foreach ($user->payments as $payment )
        {
            $result['payments'][] = [
                'paymentCode' => $payment->payment_code,
                'amount' => $payment->amount,
                'isPaid' => $payment->sucess == 0 ? false : true ,
                'createdAt' => Carbon::parse($payment->created_at)->format('Y-m-d H:i:s')

            ];
        }
        return $result;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // البحث عن الطالب المراد تحديثه
        $student = Student::find($id);

        // التحقق مما إذا كان الطالب موجودًا
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        // التحقق من صحة البيانات المرسلة
        $validatedData = $request->validate([
            'grade_level' => 'required|integer',
            'parent_id_one' => 'required|integer',
            'parent_id_two' => 'required|integer',
            'class_id' => 'required|integer',
            'semester' => 'required|integer|in:1,2,3',
        ]);

        try {
            // تحديث بيانات الطالب
            $student->update($validatedData);

            // إرجاع رسالة نجاح
            return response()->json(['message' => 'Student updated successfully'], 200);
        } catch (\Exception $e) {
            // إرجاع رسالة خطأ في حالة حدوث استثناء
            return response()->json(['error' => 'Failed to update student'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function toggleIsActive($id)
{
    $student = Student::find($id);

    if (!$student) {
        return response()->json(['error' => 'Student not found'], 404);
    }

    $user = User::find($student->user_id);

    if (!$user) {
        return response()->json(['error' => 'User not found for this student'], 404);
    }

    $user->status = $user->status == 0 ? 1 : 0;

    $user->save();

    $status = $user->status == 1 ? 'active' : 'inactive';
    return response()->json(['message' => "Student status toggled successfully. Now the student is $status"], 200);
}

    public function DownloadStudentTemplate()
    {
        $filePath = public_path("storage/uploads/importStudent.xlsx");
        $filename = 'importStudent.xlsx';
        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }

    public function importStudent(Request $request){
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $importStudent = new ImportStudent();
            Excel::import($importStudent, $file);
            return response()->json(['success', $importStudent->counter.' Students imported successfully']);
        }
        return response()->json(['error'=>'No File Provided'],401);
    }

    public function generatePaymentCodeForStudent(Request $request)
    {
        $payment = new PaymentController();
        if($payment->createPaymentCode($request->id,$request->amount))
            return response()->json(['success'=>'Student bill making has been done']);
        return response()->json(['error'=>'Student Not Found']);
    }

    public function assignCodeToAllStudents()
    {
        $students = Student::all();
        $payment = new PaymentController();
        $numberOfStudents = $students->count();
        $codeCounter = 0;
        foreach ($students as $student) {
            if($payment->createPaymentCode($student->id,1000))
               $codeCounter++;
        }
        return response()->json(['success'=> $codeCounter . ' Student out of '.$numberOfStudents.' has payment codes']);

    }

}

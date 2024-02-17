<?php

namespace App\Http\Controllers;
use App\Models\Student;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        //
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
            // Save the user
            $user->save();

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
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

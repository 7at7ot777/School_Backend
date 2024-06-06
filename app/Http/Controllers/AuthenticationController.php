<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    public function register(Request $request)
    {
//        dd('hi');
//        $this->validate($request, [
//            'name' => 'required|min:3',
//            'email' => 'required|email|unique:users',
//            'password' => 'required|min:6',
//        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        $user->save();

        $token = $user->createToken('access_token')->accessToken;

        return response()->json(['token' => $token], 200);
    }


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }


    /**
     * Handles Login Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $token = Auth::user()->createToken('auth_token')->plainTextToken;
            $user = Auth::user();
            if ($user->status == 0)
                return response()->json(['error' => 'your account is disabled'], 400);
            if ($user->user_type == 'employee') {
                $employee = $this->getEmployee($user->id);
                if (!$employee) {
                    return response()->json(['error' => 'Employee not found'], 404);
                }
                return response()->json(['token' => $token, 'user' => $employee], 200);
            } elseif ($user->user_type == 'student') {
                return response()->json(['token' => $token, 'user' => $this->getStudent($user->id)], 200);

            } else {
                return response()->json('parent');

            }
        }

        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    private function getEmployee($userId)
    {
        $employee = Employee::with('user', 'department')->where('user_id', $userId)
            ->first();
        if ($employee->role == 'teacher') {
            $employee->load('subject');
        }
        return $employee;
    }

    private function getStudent($userId)
    {
        $user = User::find($userId);
        $user->load(['student.father', 'student.mother', 'student.classroom', 'payments']);
        $result = [
            'user' =>
                [
                    'id' => $user->id,
                    'student_id' => $user->student->id,
                    'name' => $user->name ?? null,
                    'phone' => $user->phone ?? null,
                    'address' => $user->address ?? null,
                    'status' => $user->status ?? null,
                    'email' => $user->email ?? null,
                    'avatarUrl' => $user->avatar_url ?? null,
                    'userType' => $user->user_type ?? null,
                    'grade' => $user->student->grade_level ?? null,
                    'isFirstTimeLogin' => $user->isFirstTimeLogin,
                ],
            'role' => 'student',
            'class' => [
                'id' => $user->student->classroom->id ?? null,
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

        foreach ($user->payments as $payment) {
            $result['payments'][] = [
                'paymentCode' => $payment->payment_code,
                'amount' => $payment->amount,
                'isPaid' => $payment->sucess == 0 ? false : true,
                'createdAt' => Carbon::parse($payment->created_at)->format('Y-m-d H:i:s')

            ];
        }
        return $result;
    }

    public function test()
    {
        return 'test';

    }

    public function loginUsingFaceRecogintion(Request $request){
        $request->validate([
            'image' => 'required|image|max:10240', // max 10MB
        ]);
         $faceRecogintion = new FaceRecognitionController();
         $response = $faceRecogintion->detect($request);
         if ($response->status() == 201)
         {$responseData = $response->getContent();
             $responseData = json_decode($responseData, true);
             // Access the 'user_id' or other relevant data from the response
             $userId = $responseData['user_id']; // Assuming 'user_id' is returned in the response
            $user = User::find($userId);
            if ($user) {
                if ($user->status == 0) {return response()->json(['error' => 'Your account is disabled'], 400); }
                // Generate a token for the user
                $token = $user->createToken('auth_token')->plainTextToken;

                // Check the user type and return the appropriate response
                if ($user->user_type == 'employee') {
                    $employee = $this->getEmployee($user->id);
                    if (!$employee) { return response()->json(['error' => 'Employee not found'], 404);}
                    return response()->json(['token' => $token, 'user' => $employee], 200);
                } elseif ($user->user_type == 'student') {
                    return response()->json(['token' => $token, 'user' => $this->getStudent($user->id)], 200);
                } else {
                    return response()->json(['token' => $token, 'user' => 'parent'], 200);
                }
            }}
         return response()->json(['error' => 'User not found'], 404);
          }


}

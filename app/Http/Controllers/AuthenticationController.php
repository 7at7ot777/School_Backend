<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
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
            if($user->user_type == 'employee')
            {
                $employee = $this->getEmployee($user->id);
                if (!$employee)
                {
                    return response()->json(['error' => 'Employee not found'], 404);
                }
                return response()->json(['token'=>$token, 'employee'=>$employee],200);
            }elseif($user->user_type == 'student'){
                return response()->json('student');
            }else{
                return response()->json('parent');

            }
        }

        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    private function getEmployee($userId){
        $employee = Employee::with('user','department')->find($userId);
        return $employee;
    }

    public function test()
    {
        return 'test';

    }
}

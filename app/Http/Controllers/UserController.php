<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{

    public function show($id)
    {
        $user = User::find($id);

        $formatedUser = null;

        switch ($user->user_type){
            case 'student':
              $user->load(['student.father','student.mother','student.classroom']);
              $formatedUser = $this->formatStudent($user);
                break;
            case 'parent' :
                //TODO: to be implemented
                $this->getParentDate($user);
                break;
            case 'employee':
                $user->load(['employee.department','employee.subject']);
                break;
        }
        return $formatedUser;
    }


    private function formatStudent($user){

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
        ];
        return $result;
    }
 private function getEmployeeDate($user){

    }
 private function getParentDate($user){

    }


    public function resetPassword($userId)
    {
        // Retrieve the user by ID
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Reset the user's password to 'welcome'
        $user->password = bcrypt('welcome');
        $user->save();

        return response()->json(['message' => 'Password reset successfully']);
    }

    public function setPassword(Request $request,$userId)
    {
        // Retrieve the user by ID
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        if (Hash::check($request->current_password, $user->password)) {
            // Update the password to the new password
            $user->password = bcrypt($request->new_password);
            $user->save();

            return response()->json(['message' => 'Password is set successfully']);
        } else {
            return response()->json(['error' => 'Current password is incorrect'], 401);
        }

    }


}

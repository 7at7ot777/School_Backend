<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function show($id)
    {
        $user = User::find($id);

        switch ($user->user_type){
            case 'student':
               return $user->load('student');
                break;
            case 'parent' :
                $this->getParentDate($user);
                break;
            case 'employee':
                $this->getEmployeeDate($user);
                break;
        }
        return $user;
    }

    private function getStudentDate(User $user){

        return $user->load('student');

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

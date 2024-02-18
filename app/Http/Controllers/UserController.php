<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{

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

    public function uploadAvatar(Request $request,$id)
    {
        $validator = Validator::make($request->all(),[
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048']);
        if ($validator->fails()) {
            return $validator->errors();
        }
        $user = User::find($id);
        if(!$user) {
            return response()->json(['error' => 'User Not Found'],404);

        }
        $image = $request->file('image');
        $imageName = time().'.'.$image->extension();

        $image->move(public_path('storage/avatars'), $imageName);

        $user->avatar_url =  url('storage/avatars/'.$imageName);
        $user->save();

        return response()->json(['success' => true, 'image' => $user->avatar_url]);
    }


}

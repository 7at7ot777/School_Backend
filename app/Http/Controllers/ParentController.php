<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Parents;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class ParentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $parents = Parents::all();
        return response()->json($parents, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'email' => 'required|string|email|unique:users|max:255', // تأكد من أن البريد الإلكتروني فريد في جدول المستخدمين
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $user = User::create([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'password' => bcrypt('welcome'),
            'email' => $request->input('email'),
            'user_type' => 'parent'
        ]);

        // Create a new parent instance and associate it with the user
        $parent = new Parents([
            'user_id' => $user->id,
        ]);

        // Save the parent to the database
        $parent->save();

        // Return a success response
        return response()->json(['message' => 'Parent created successfully'], 201);
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
        $parent = Parents::find($id);

    // Check if the parent record exists
    if (!$parent) {
        return response()->json(['error' => 'Parent not found'], 404);
    }

    // Update the user associated with the parent
    $user = $parent->user;
    $user->name = $request->input('name');
    $user->phone = $request->input('phone');
    $user->address = $request->input('address');
    $user->email = $request->input('email');
    $user->save();

    // Return a success response
    return response()->json(['message' => 'Parent updated successfully'], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $parent = Parents::find($id);

        if (!$parent) {
            return response()->json(['error' => 'Parent not found'], 404);
        }
    
        $user = User::find($parent->user_id);
    
        if (!$user) {
            return response()->json(['error' => 'User not found for this parent'], 404);
        }
    
        $user->status = $user->status == 0 ? 1 : 0;
    
        $user->save();
    
        $status = $user->status == 1 ? 'active' : 'inactive';
        return response()->json(['message' => "Parent status toggled successfully. Now the parent is $status"], 200);
    
    }
}
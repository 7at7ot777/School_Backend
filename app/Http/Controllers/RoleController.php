<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    private static $rules = [
        'name' => 'required|unique:roles|max:255',
        'description' => 'nullable|max:255',
    ];

    private static $errorMessages = [
        'name.required' => 'The role name field is required.',
        'name.unique' => 'The specified role name is already taken.',
        'name.max' => 'The role name field must not exceed 255 characters.',
        'description.max' => 'The description field must not exceed 255 characters.',
    ];

    public function index()
    {
        $roles = Role::all();
        return response()->json($roles);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), self::$rules, self::$errorMessages);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            $role = Role::create($request->all());
        }
        return response()->json($role, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['error' => 'Role not found'], 404);
        }

        return response()->json($role);
;

    }


    /**
     * Update the specified resource in storage.
     */
    public
    function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), self::$rules, self::$errorMessages);
        if ($validator->fails()) {
            return $validator->errors();
        } else {

            $role->update($request->all());

            return response()->json($role);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public
    function destroy($id)
    {

        $role = Role::find($id);

        if (!$role) {
            return response()->json(['error' => 'Role not found'], 404);
        }

        $role->delete();

        return response()->json(['success' => 'Role deleted successfully'], 200);
    }
}

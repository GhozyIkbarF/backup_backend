<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(Request $request)
    {
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->email = $request->role;
        $user->save();

        return response()->json(['message' => 'User created successfully'], 201);
    }

    public function update(Request $request, $id)
    {
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->email = $request->role;

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json(['message' => 'User updated successfully'], 200);
    }

    public function destroy($id)
    {

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}

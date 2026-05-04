<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function makeReviewer(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->role = 'reviewer';
        $user->save();

        return response()->json([
            'message' => 'User promoted to reviewer successfully.',
            'user' => $user
        ]);
    }

    public function makeEditor(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->role = 'editor';
        $user->save();

        return response()->json([
            'message' => 'User promoted to editor successfully.',
            'user' => $user
        ]);
    }

    public function search(Request $request)
    {
        $name = $request->query('name');

        $query = User::query()->orderBy('name', 'asc');

        if ($name) {
            $query->where('name', 'like', "%{$name}%");
        }

        return response()->json($query->get());
    }

    public function update(Request $request, $id)
    {
        $authUser = $request->user();
        $user = User::findOrFail($id);

        if ($authUser->id !== $user->id && $authUser->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => "sometimes|string|email|max:255|unique:users,email,$id",
            'password' => 'sometimes|string|min:8',
            'profile_picture' => 'nullable|string',
            'background' => 'nullable|string',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }
}

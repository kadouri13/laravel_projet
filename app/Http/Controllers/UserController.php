<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @OA\Put(
     *     path="/users/{id}/make-reviewer",
     *     summary="Promote a user to reviewer role (Admin only)",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="User promoted successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden (Admin only)"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/users/search",
     *     summary="Search users by name",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Name to search for",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of matching users",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function search(Request $request)
    {
        $name = $request->query('name');

        $query = User::query();

        if ($name) {
            $query->where('name', 'like', "%{$name}%");
        }

        return response()->json($query->get());
    }

    public function update(Request $request, $id)
    {
        $authUser = $request->user();
        $user = User::findOrFail($id);

        // Prevent random users from editing others
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

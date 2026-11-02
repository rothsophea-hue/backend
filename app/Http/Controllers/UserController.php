<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Sort
            $sortBy = $request->query('sortBy', 'id');
            $sortDir = $request->query('sortDir', 'desc');

            // Pagination
            $per_page = $request->input('perpage', 5);

            // Search + Relationship
            $search = $request->input('search');

            $users = User::with('orders')
                ->when($search, function ($query, $search) {
                    return $query->where('name', 'LIKE', "%{$search}%");
                })
                ->orderBy($sortBy, $sortDir)
                ->paginate($per_page);

            return $users;

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
    {
        $user = User::with('orders')->findOrFail($id);

        return $user;
    }

    public function store(Request $request)
    {
        try {
            $validate = $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'role'     => 'required|string|max:50',
            ]);

            $user = User::create([
                'name'     => $validate['name'],
                'email'    => $validate['email'],
                'password' => $validate['password'],
                'role'     => $validate['role'],
            ]);

            return $user;

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try{
            $validate = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email',
            'password' => 'sometimes|string|min:6',
            'role'     => 'sometimes|string|max:50',
        ]);

        $user = User::findOrFail($id);

        $user->update($validate);

        return $user;
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
        
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        // If the application defines an active status, reject inactive accounts here.
        // For example: if ($user->status !== 'active') { ... }

        $token = $user->createToken('pos-client')->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(null, 204);
    }
}

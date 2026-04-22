<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $users = User::all();
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name_one' => 'required|string|max:255',
            'last_name_second' => 'nullable|string|max:255',
            'dni' => 'required|string|unique:users,dni|max:20',
            'phone' => 'required|string|max:20',
            'email' => 'required|string|email|unique:users,email|max:255',
            'shipping_address' => 'required|string|max:255',
            'shipping_zip_code' => 'required|string|max:10',
            'shipping_province' => 'required|string|max:100',
            'shipping_country' => 'nullable|string|max:100',
            'billing_address' => 'required|string|max:255',
            'billing_zip_code' => 'required|string|max:10',
            'billing_province' => 'required|string|max:100',
            'billing_country' => 'nullable|string|max:100',
            'password' => 'required|string|min:8',
            'role' => 'nullable|in:admin,user',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        $user = User::create($validated);
        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $user = User::with('orders')->findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'last_name_one' => 'sometimes|string|max:255',
            'last_name_second' => 'nullable|string|max:255',
            'dni' => 'sometimes|string|unique:users,dni,' . $id . '|max:20',
            'phone' => 'sometimes|string|max:20',
            'email' => 'sometimes|string|email|unique:users,email,' . $id . '|max:255',
            'shipping_address' => 'sometimes|string|max:255',
            'shipping_zip_code' => 'sometimes|string|max:10',
            'shipping_province' => 'sometimes|string|max:100',
            'shipping_country' => 'sometimes|string|max:100',
            'billing_address' => 'sometimes|string|max:255',
            'billing_zip_code' => 'sometimes|string|max:10',
            'billing_province' => 'sometimes|string|max:100',
            'billing_country' => 'sometimes|string|max:100',
            'password' => 'sometimes|string|min:8',
            'role' => 'sometimes|in:admin,user',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $user->update($validated);
        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}

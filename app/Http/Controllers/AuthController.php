<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Registrar un nou usuari i enviar email de verificació
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name_one' => 'required|string|max:255',
            'dni' => 'required|string|unique:users',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:users',
            'address' => 'required|string|max:255',
            'zip_code' => 'required|string|max:10',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'last_name_one' => $request->last_name_one,
            'last_name_second' => $request->last_name_second,
            'dni' => $request->dni,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'zip_code' => $request->zip_code,
            'password' => $request->password,
            'role' => 'user',
        ]);

        // Enviar email de verificació
        $user->notify(new VerifyEmailNotification());

        // Crear token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Usuari creat correctament. Verifica el teu correu electrònic.',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Iniciar sesión y retornar token
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            // Crear token para el usuario
            $token = $request->user()->createToken('auth-token')->plainTextToken;

            return response()->json([
                'message' => 'Login exitoso',
                'user' => $request->user(),
                'token' => $token,
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['Las credenciales no son correctas.'],
        ]);
    }

    /**
     * Cerrar sesión y revoke tokens
     */
    public function logout(Request $request)
    {
        // Revocar todos los tokens del usuario
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout exitoso',
        ]);
    }

    /**
     * Obtener usuario autenticado
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}

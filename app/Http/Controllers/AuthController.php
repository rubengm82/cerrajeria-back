<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
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

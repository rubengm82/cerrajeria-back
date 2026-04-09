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
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'last_name_one' => $request->last_name_one,
            'last_name_second' => $request->last_name_second,
            'email' => $request->email,
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

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales no son correctas.'],
            ]);
        }

        $user = $request->user();

        // Verificar que el email esté verificado
        // y crear token para el usuario
        return !$user->hasVerifiedEmail()
            ? response()->json([
                'message' => 'Has de verificar el teu correu electrònic abans d\'accedir.',
                'email_verified' => false,
            ], 403)
            : response()->json([
                'message' => 'Login exitoso',
                'user' => $user,
                'token' => $user->createToken('auth-token')->plainTextToken,
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

    /**
     * Reenviar email de verificación (sin estar logueado)
     */
    public function resendVerificationEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Siempre devolvemos el mismo mensaje por seguridad
        // para no revelar si el email existe o no
        if ($user && !$user->hasVerifiedEmail()) {
            $user->notify(new VerifyEmailNotification());
        }

        return response()->json([
            'message' => 'Si el correu electrònic existeix i no està verificat, s\'ha enviat un nou enllaç de verificació.',
        ]);
    }
}

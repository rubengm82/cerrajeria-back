<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    /**
     * Envía el email de verificación al usuario.
     */
    public function sendVerificationEmail(Request $request)
    {
        $user = Auth::user();
        $message = null;
        $status = 200;

        if (!$user) {
            $message = 'No autenticat';
            $status = 401;
        } elseif ($user->hasVerifiedEmail()) {
            $message = 'El correu ja està verificat';
        } else {
            $user->notify(new VerifyEmailNotification());
            $message = 'Correu de verificació enviat';
        }

        return response()->json(['message' => $message], $status);
    }

    /**
     * Verifica el email del usuario.
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);
        $message = null;
        $status = 200;

        if (!hash_equals(sha1($user->email), $hash)) {
            $message = 'Hash invàlid';
            $status = 400;
        } elseif ($user->hasVerifiedEmail()) {
            $message = 'El correu ja està verificat';
        } else {
            $user->markEmailAsVerified();
            $message = 'Correu verificat correctament';
        }

        return response()->json(['message' => $message], $status);
    }
}

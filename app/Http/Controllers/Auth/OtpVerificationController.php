<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpCodeMail;
use App\Models\EmailOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

/**
 * Vérification de l'email par code OTP (remplace le lien Breeze par défaut).
 * L'utilisateur est déjà connecté (mais non vérifié) : le middleware `verified`
 * des routes protégées le renvoie ici tant que ce n'est pas fait.
 */
class OtpVerificationController extends Controller
{
    public function notice(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended('/')
            : view('auth.verify-otp');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:'.EmailOtp::CODE_LENGTH],
        ]);

        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended('/');
        }

        if (! EmailOtp::attempt($user->email, 'register', $request->input('code'))) {
            throw ValidationException::withMessages([
                'code' => 'Code invalide ou expiré. Vérifiez votre saisie ou demandez un nouveau code.',
            ]);
        }

        $user->markEmailAsVerified();

        return redirect()->intended('/')->with('status', 'email-verified');
    }

    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended('/');
        }

        $code = EmailOtp::issue($user->email, 'register');
        Mail::to($user->email)->send(new OtpCodeMail($code, $user->name));

        return back()->with('status', 'otp-resent');
    }
}

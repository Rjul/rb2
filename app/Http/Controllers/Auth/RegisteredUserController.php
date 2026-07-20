<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpCodeMail;
use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Inscription : le compte est créé NON vérifié, un code OTP est envoyé,
     * et l'utilisateur est redirigé vers la saisie du code. Tant que l'email
     * n'est pas vérifié, l'accès protégé (poster un commentaire) est refusé.
     */
    public function store(Request $request)
    {
        // Honeypot : champ caché que seuls les bots remplissent. On mime un
        // succès (aucun compte créé, aucun email envoyé → quota préservé).
        if (filled($request->input('website'))) {
            return redirect()->route('login')->with('status', 'verification-link-sent');
        }

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => mb_strtolower(trim($request->email)),
            'password' => Hash::make($request->password),
            // email_verified_at reste null → vérification OTP requise.
        ]);

        $code = EmailOtp::issue($user->email, 'register');
        Mail::to($user->email)->send(new OtpCodeMail($code, $user->name));

        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}

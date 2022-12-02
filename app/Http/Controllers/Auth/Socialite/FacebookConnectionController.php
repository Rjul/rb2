<?php

namespace App\Http\Controllers\Auth\Socialite;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use Exception;


class FacebookConnectionController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }


    /**
     * Create a new controller instance.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleFacebookCallback()
    {
        try {
            $user = Socialite::driver('facebook')->user();
            // find user by google id
            $finduser = User::where('fb_id', $user->id)->first();
            if ($finduser) {

                Auth::login($finduser);
                return redirect()->intended('dashboard');

            }
            $finduser = User::where('email', $user->email)->first();

            if ($finduser) {
                Auth::login($finduser);
                return redirect()->intended('dashboard');
            }
            else {

                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'fb_id' => $user->id,
                    'password' => encrypt('123456dummy')
                ]);

                Auth::login($newUser);
                return redirect()->intended('dashboard');
            }
        } catch (Exception $e) {
            Log::error(printf('Error login with google: %s', $e->getMessage()));
        }
    }
}


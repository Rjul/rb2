<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use function Termwind\render;

class HomepageController extends Controller
{
    public function index() {
//        event(new Registered(Auth::user()));
//
//        new Mail();
        return view('homepage');
    }
}

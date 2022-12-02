<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use function Termwind\render;

class HomepageController extends Controller
{
    public function index() {
        return view('homepage');
    }
}

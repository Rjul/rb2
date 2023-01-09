<?php

namespace App\Http\Controllers;

use App\Models\Emision;
use App\Models\Programme;
use Illuminate\Http\Request;

class DetannController extends Controller
{
    public function index(Programme $programme, Emision $emision)
    {
        $suggestionEmisions = Emision::where('programme_id', $programme->id)->where('id', '!=', $emision->id)->limit(6)->get();
        return view('pages.detann', compact('programme', 'emision', 'suggestionEmisions'));
    }
}

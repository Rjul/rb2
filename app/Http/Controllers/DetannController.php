<?php

namespace App\Http\Controllers;

use App\Models\Emision;
use App\Models\Programme;
use Illuminate\Http\Request;

class DetannController extends Controller
{
    public function index(Programme $programme, Emision $emision)
    {
        // where programme is active and emiision is active or redirect to the home page
        if (!$programme->is_active || !$emision->is_active) {
            return redirect()->route('homepage');
        }

        
        $suggestionEmisions = Emision::where('programme_id', $programme->id)
            ->where('id', '!=', $emision->id)
            ->orderBy('id', 'desc')
            ->orderBy('active_at', 'desc')
            ->where('active_at', '<', now())
            ->where('emisions.is_active', "=", true)
            ->limit(6)->get();

        return view('pages.detann', compact('programme', 'emision', 'suggestionEmisions'));
    }
}

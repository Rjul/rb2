<?php

namespace App\Http\Controllers;

use App\Models\Emision;
use Illuminate\Http\Request;

class ListController extends Controller
{
    public function index
    (

    )
    {

        return view('pages.list', [
            'emisions' =>         Emision::all()
        ]);
    }
}

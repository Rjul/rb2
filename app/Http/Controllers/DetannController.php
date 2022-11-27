<?php

namespace App\Http\Controllers;

use App\Models\Emision;
use App\Models\Programme;
use Illuminate\Http\Request;

class DetannController extends Controller
{
    public function index(Programme $programme, Emision $emision)
    {
        dump($programme);
        dump($emision);
    }
}

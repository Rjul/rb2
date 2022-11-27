<?php

namespace App\Http\Controllers;

use App\Models\Emision;
use App\Models\Programme;
use Illuminate\Http\Request;

class DetannController extends Controller
{
    public function index(Programme $programme, Emision $emision)
    {

        // 1 template pour les 3 types de vue ( audio, texte, video ) avec les componant pour les elements communs

        // ou 3 templates ?

        dump($programme);
        dump($emision);
    }
}

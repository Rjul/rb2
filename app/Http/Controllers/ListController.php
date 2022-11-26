<?php

namespace App\Http\Controllers;

use App\Models\Emision;
use App\Models\Programme;
use Illuminate\Http\Request;

class ListController extends Controller
{
    public function index
    (
        Programme $programme = null
    )
    {
        $emisions = Emision::query();

        if (!is_null($programme)) {
            $emisions
                ->where('programme_id', $programme->id);
        }

        return view('pages.list', [
            'emisions' =>         $emisions->with(['programme', 'tags'])
                                                    ->paginate(25)
        ]);
    }
}

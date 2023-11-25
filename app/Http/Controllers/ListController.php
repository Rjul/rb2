<?php

namespace App\Http\Controllers;

use App\Models\Emision;
use App\Models\Programme;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ListController extends Controller
{
    public function index
    (
        Programme $programme = null,
        $tag = null,
        string $type = null
    )
    {
        $emisions = Emision::query()->with(['programme']);
        $emisions->orderBy('active_at', 'desc')
            ->where('active_at', '<', now())
            ->where('active', "=", true);

        if (!is_null($programme)) {
            $emisions
                ->where('programme_id', $programme->id);
        }
        if (!is_null($tag)) {
            $emisions->whereHas('tags', function (Builder $query) use ($tag) {
                return $query->where('slug->fr', $tag ?? 0);
            });
            $tag = Tag::query()->where('slug->fr', $tag ?? 0)->limit(1)->get()->first();
        }
        if (!is_null($type)) {
            $emisions
                ->where('media_type', $type);
        }

        return view('pages.list', [
            'programme'         => $programme,
            'tag'               => $tag,
            'type'              => $type,
            'query'             => null,
            'emisions'          => $emisions->paginate(25)
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Models\Emision;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;

class SearchController extends Controller
{

    public function index(SearchRequest $request) {
        return view('pages.list', [
            'emisions' => $this->getSearchEmision(Emision::query(), $request)
        ]);
    }

    protected function getSearchEmision(Builder $emisions, SearchRequest $request)
    {
        dump($request->validated());


        if ($request->has('programmes_id')) {
            collect($request->get('programmes_id'))
                ->each(function ($id, $key) use ($emisions) {
                    if ($key === 0) {
                        $emisions
                            ->where('programme_id', $id);
                    } else {
                        $emisions
                            ->orWhere('programme_id', $id);
                    }

                });
        }

        if ($request->has('tags_id')) {
            collect($request->get('tags_id'))
                ->each(function ($tag) use ($emisions) {
                    $emisions->whereHas(
                        'tags',
                        fn (\Illuminate\Database\Eloquent\Builder $query) => $query->where('tags.id', $tag )
                    );
                });
        }
        if ($request->has('groups_programme_id')) {
            $emisions
                ->leftJoin('programmes', 'emisions.programme_id', '=', 'programmes.id')
                ->select('emisions.*');
            collect($request->get('groups_programme_id'))
                ->each(function ($id, $key) use ($emisions) {
                    if ($key === 0) {
                        $emisions
                            ->where('programmes.group_programme_id', $id);
                    } else {
                        $emisions
                            ->orWhere('programmes.group_programme_id', $id);
                    }
                });
        }
        return $emisions->paginate(25);
    }
}

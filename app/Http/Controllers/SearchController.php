<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Models\Emision;
use App\Models\GroupProgramme;
use App\Models\Programme;
use App\Models\Tag;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;

class SearchController extends Controller
{

    public function index(SearchRequest $request) {
        return view('pages.list', [
            'emisions' => $this->searchEmision(Emision::query(), $request)
                ->paginate(25)->appends($request->validated()),
            'programme'         => null,
            'tag'               => null,
            'type'              => null,
            'query'            => $request->input('query') ?? null,
        ]);
    }

    public function suggestion(SearchRequest $request)
    {
        if ($request->has('query')) {
            $query = [ $request->get('query') ];
//            $query = explode(' ', $request->get('query'));
            $groupsProgramme = $this->searchInNameAndDescription(GroupProgramme::query(), $query);
            $programmes = $this->searchInNameAndDescription(Programme::query(), $query);
            $emisions = $this->searchInNameAndDescription(Emision::query(), $query);
            $tags = $this->searchTagInNameAndDescription(Tag::query(), $query);
        } else {
            abort(400);
        }

        if (env('APP_ENV') === 'local') {
            debugbar()->disable();
        }

        return view('components.header.suggestion', [
            'query'             => $request->get('query'),
            'groups_programme'  => $groupsProgramme->limit(4)->get(),
            'programmes'         => $programmes->limit(10)->get(),
            'emisions'          => $emisions
                ->orderBy('emisions.active_at', 'desc')
                ->where('emisions.active_at', '<', now())
                ->limit(3)
                ->get(),
            'tags'              => $tags->limit(10)->get(),
        ]);
    }

    protected function searchEmision(Builder $emisions, SearchRequest $request): Builder
    {
        if ($request->has('programmes_id')) {
            $emisions = $this->searchByProgramme($emisions, $request);
        }
        if ($request->has('query')) {
            $emisions = $this->searchByQuery($emisions, $request);
        }
        if ($request->has('tags_id')) {
            $emisions = $this->searchByTags($emisions, $request);
        }
        if ($request->has('groups_programme_id')) {
            $emisions = $this->searchByGroupsProgramme($emisions, $request);
        }
        if ($request->has('type')) {
            $emisions = $this->searchByType($emisions, $request);
        }
        if ($request->has('duration')) {
            $emisions = $this->searchByDuration($emisions, $request);
        }
        $emisions->orderBy('active_at', 'desc')
            ->whereHas('programme', function (Builder $query) {
                $query->where('is_active', "=", true);
            })
            ->where('active_at', '<', now())
            ->where('emisions.is_active', "=", true)
        ;
        return $emisions;
    }

    private function searchTagInNameAndDescription(Builder $model, array $query): Builder
    {
        collect($query)->each(function ($value) use ($model) {
            $model
                ->where('name->fr', 'LIKE' , '%'.$value.'%')
                ->orWhere('description', 'LIKE' , '%'.$value.'%');
        });
        return $model;
    }

    private function searchInNameAndDescription(Builder $model, array $query): Builder
    {
        collect($query)->each(function ($value) use ($model) {
            $model
                ->where('name', 'LIKE' , '%'.$value.'%')
                ->orWhere('description', 'LIKE' , '%'.$value.'%')
            ;
        });
        return $model;
    }

    private function searchByType(Builder $model, SearchRequest $request): Builder
    {
        collect($request->get('type'))->each(function ($value, $key) use ($model) {
            if ($key === 0) {
                $model
                    ->where('media_type', $value);
            } else {
                $model
                    ->orWhere('media_type', $value);
            }
        });
        return $model;
    }

    private function searchByDuration(Builder $model, SearchRequest $request): Builder
    {
        collect($request->get('duration'))->each(function ($value, $key) use ($model) {
            if ($key === 0) {
                $model
                    ->where('duration', '<',  $value);
            }
        });
        return $model;
    }

    private function searchByProgramme(Builder $emisions, SearchRequest $request): Builder
    {
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

        return $emisions;
    }

    private function searchByQuery(Builder $emisions, SearchRequest $request): Builder
    {
        collect($request->get('query'))
            ->each(function ($value) use ($emisions) {
                collect(explode(' ', $value))
                    ->each(function ($keyQuery) use ($emisions) {
                        $emisions
                            ->where('name', 'LIKE' , '%'.$keyQuery.'%')
                            ->orWhere('description', 'LIKE' , '%'.$keyQuery.'%');
                    });
            });

        return $emisions;
    }

    private function searchByTags(Builder $emisions, SearchRequest $request): Builder
    {
        collect($request->get('tags_id'))
            ->each(function ($tag) use ($emisions) {
                $emisions->whereHas(
                    'tags',
                    fn (\Illuminate\Database\Eloquent\Builder $query) => $query->where('tags.id', $tag )
                );
            });

        return $emisions;
    }

    private function searchByGroupsProgramme(Builder $emisions, SearchRequest $request): Builder
    {
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
        return $emisions;
    }
}

<?php

namespace App\Orchid\Layouts\Emissions;

use App\Models\Emision as Emission;
use App\Models\Programme;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Spatie\Tags\Tag;

class EmissionsListLayout extends Table
{
    /**
     * Data source.
     *
     * @var string
     */
    public $target = 'emissions';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('title', 'Titre')
                ->sort()
                ->render(function (Emission $emission) {
                    return Link::make($emission->title)
                        ->route('platform.emission.edit', $emission);
                }),
            TD::make('programme.name', 'Programme')
                ->sort()
                ->render(function (Emission $emission) {
                    return Link::make($emission->programme->name)
                        ->route('platform.programme.edit', $emission->programme->id);
            }),
            TD::make('media_type', 'Type de média')
                ->sort()
                ->render(function (Emission $emission) {
                    return strtoupper($emission->media_type);
                }),
            TD::make('active', 'Active')
                ->sort()
                ->render(function (Emission $emission) {
                    return $emission->active ? 'Oui' : 'Non';
                }),
            TD::make('emission.active_at', 'Date de publication')
                ->sort()
                ->render(function (Emission $emission) {
                    return $emission->active_at;
                }),
        ];
    }
}

<?php

namespace App\Orchid\Layouts\GroupProgramme;

use App\Models\GroupProgramme;
use App\Models\Programme;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Spatie\Tags\Tag;

class GroupProgrammesListLayout extends Table
{
    /**
     * Data source.
     *
     * @var string
     */
    public $target = 'programmes';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('programme.name', 'Nom')
                ->sort()
                ->render(function (GroupProgramme $programme) {
                    return Link::make($programme->name)
                        ->route('platform.group.programme.edit', $programme);
                }),
            TD::make('tag.active', 'Active')
                ->sort()
                ->render(function (GroupProgramme $programme) {
                    return $programme->active ? 'Oui' : 'Non';
            }),
        ];
    }
}

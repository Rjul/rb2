<?php

namespace App\Orchid\Layouts\Programme;

use App\Models\Programme;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\AsSource;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Tags\Tag;

class ProgrammesListLayout extends Table
{

    use AsSource, Attachable, Filterable;
    /**
     * Data source.
     *
     * @var string
     */
    public $target = 'programmes';

    /**
     * Name of columns to which http filter can be applied
     *
     * @var array
     */
    protected $allowedFilters = [
        'programme.name',
        'programme.group_programme_id',
        'programme.is_active'

    ];

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('name', 'Nom')
                ->sort()
                ->filter(Input::make('name'))
                ->render(function (Programme $programme) {
                    return Link::make($programme->name)
                        ->route('platform.programme.edit', $programme);
                }),
            TD::make('group_programme.name', 'Groupe programme')
                ->render(function (Programme $programme) {
                    return Link::make($programme->group_programme->name)
                        ->route('platform.group.programme.edit', $programme->group_programme->id);
            }),
            TD::make('is_active', 'Active')
                ->sort()
                ->filter(Switcher::make('is_active')->sendTrueOrFalse())
                ->render(function (Programme $programme) {
                    return $programme->is_active ? 'Oui' : 'Non';
                }),
            TD::make('is_archived', 'Archivé')
                ->sort()
                ->filter(Switcher::make('is_archived')->sendTrueOrFalse())
                ->render(function (Programme $programme) {
                    return $programme->is_archived ? 'Oui' : 'Non';
                }),
        ];
    }
}

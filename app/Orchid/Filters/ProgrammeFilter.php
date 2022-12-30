<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;

class ProgrammeFilter extends Filter
{
    /**
     * The displayable name of the filter.
     *
     * @return string
     */
    public function name(): string
    {
        return 'Programme';
    }

    /**
     * The array of matched parameters.
     *
     * @return array|null
     */
    public function parameters(): ?array
    {
        return ['filter.programme'];
    }

    /**
     * Apply to a given Eloquent query builder.
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function run(Builder $builder): Builder
    {
        return $builder->join('programmes', 'emisions.programme_id', '=', 'programmes.id', 'inner')
        ->select('emisions.*')
        ->where('programmes.name', 'LIKE', '%'.$this->request->get('filter')['programme'].'%');
    }

    /**
     * Get the display fields.
     *
     * @return Field[]
     */
    public function display(): iterable
    {
        return [
            Input::make('Nom de programme')
                ->type('text')
                ->value($this->request->get('filter')['programme'])
                ->placeholder('Search...')
                ->title('Search')
        ];
    }
}

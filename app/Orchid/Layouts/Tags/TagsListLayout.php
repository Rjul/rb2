<?php

namespace App\Orchid\Layouts\Tags;

use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Spatie\Tags\Tag;

class TagsListLayout extends Table
{
    /**
     * Data source.
     *
     * @var string
     */
    public $target = 'tags';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('tag.name', 'Nom')
                ->render(function (Tag $tag) {
                    return Link::make($tag->name)
                        ->route('platform.tag.edit', $tag);
                }),
            TD::make('tag.color', 'Last edit')
                ->render(function (Tag $tag) {
                    return '<div class="tag" style="background-color:'.$tag->color.'">'.$tag->color.'.</div>';
            }),
        ];
    }
}

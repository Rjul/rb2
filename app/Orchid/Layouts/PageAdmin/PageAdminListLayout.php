<?php

namespace App\Orchid\Layouts\PageAdmin;

use App\Models\GroupProgramme;
use App\Models\PageAdmin;
use App\Models\Programme;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Spatie\Tags\Tag;

class PageAdminListLayout extends Table
{
    /**
     * Data source.
     *
     * @var string
     */
    public $target = 'pageAdmin';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('name', 'Nom')
                ->sort()
                ->render(function (PageAdmin $pageAdmin) {
                    return Link::make($pageAdmin->name)
                        ->route('platform.page-admin.edit', $pageAdmin);
                })
                ,
            TD::make('path', 'url')
                ->sort(),
        ];
    }
}

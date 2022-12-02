<?php

namespace App\Orchid\Layouts;

use App\Models\Emision as Emission;
use App\Models\Programme;
use App\Models\WebsiteNew;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Spatie\Tags\Tag;

class WebsiteNewListLayout extends Table
{
    /**
     * Data source.
     *
     * @var string
     */
    public $target = 'websiteNews';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('websiteNews.name', 'Nom')
                ->sort()
                ->render(function (WebsiteNew $news) {
                    return Link::make($news->name)
                        ->route('platform.annonce.edit', $news);
                }),
            TD::make('websiteNews.active', 'Active')
                ->sort()
                ->render(function (WebsiteNew $new) {
                    return $new->active ? 'Oui' : 'Non';
                }),
            TD::make('websiteNews.active_at', 'Date de publication')
                ->sort()
                ->render(function (WebsiteNew $new) {
                    return $new->active_at;
                }),
            TD::make('websiteNews.end_at', 'Date de fin')
                ->sort()
                ->render(function (WebsiteNew $new) {
                    return $new->end_at;
                }),
        ];
    }
}

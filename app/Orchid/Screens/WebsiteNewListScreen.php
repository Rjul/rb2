<?php

namespace App\Orchid\Screens;

use App\Models\Emision as Emission;
use App\Models\WebsiteNew;
use App\Orchid\Layouts\WebsiteNewListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;

class WebsiteNewListScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'websiteNews' => WebsiteNew::filters()->defaultSort('active_at')->paginate()
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Annonces';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Nouvelle annonce')
                ->icon('pencil')
                ->route('platform.annonce.edit')
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            WebsiteNewListLayout::class
            ];
    }
}

<?php

namespace App\Orchid\Layouts\Emissions;

use App\Models\Emision as Emission;
use App\Models\Programme;
use App\Orchid\Filters\ProgrammeFilter;
use Illuminate\Support\Carbon;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Select;
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
            TD::make('name', 'Titre')
                ->sort()
                ->filter()
                ->render(function (Emission $emission) {
                    return Link::make($emission->name)
                        ->route($emission->media_type === 'audio' ? 'platform.emission.edit' : ($emission->media_type === 'video' ? 'platform.emission.video.edit' : 'platform.emission.text.edit')
                            , $emission);
                }),
            TD::make('programme', 'Programme')
                ->sort()
                ->filter(ProgrammeFilter::class)
                ->render(function (Emission $emission) {
                    return Link::make($emission->programme->name)
                        ->route('platform.programme.edit', $emission->programme->id);
            }),
            TD::make('media_type', 'Type de média')
                ->sort()
                ->filter(Select::make('media_type')->options([
                    'TEXT' => 'Texte' ,
                    'AUDIO' => 'Audio',
                    'VIDEO' => 'Video'
                ]))
                ->render(function (Emission $emission) {
                    return strtoupper($emission->media_type);
                }),
            TD::make('is_active', 'Active')
                ->sort()
                ->filter(Select::make('is_active')->options([
                    true => 'Oui' ,
                    false => 'Non'
                ]))
                ->render(function (Emission $emission) {
                    return $emission->is_active ? 'Oui' : 'Non';
                }),
            TD::make('active_at', 'Date de publication')
                ->sort()
                ->render(function (Emission $emission) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $emission->active_at)->locale('fr_FR')->toFormattedDateString();
                }),
        ];
    }
}

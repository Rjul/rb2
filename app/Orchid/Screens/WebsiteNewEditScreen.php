<?php

namespace App\Orchid\Screens;

use App\Models\Emision as Emission;
use App\Models\Programme;
use App\Models\GroupProgramme;
use App\Models\WebsiteNew;
use App\Orchid\Overrides\UploadOverRide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Fields\TextArea;
use Orchid\Support\Facades\Alert;
use Orchid\Platform\Models\User;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Fields\DateRange;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Map;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\RadioButtons;
use Orchid\Screen\Fields\Range;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Fields\UTM;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Spatie\Tags\Tag;

class WebsiteNewEditScreen extends Screen
{
    public WebsiteNew $websiteNews;

    /**
     * Query data.
     *
     * @return array
     */
    public function query(WebsiteNew $websiteNews): iterable
    {
        return [
            'websiteNews'  => $websiteNews,
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->websiteNews->exists ? 'Edition annonce' : 'Nouvelle annonce';
    }

    /**
     * Display header description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return 'Configuration et creation d\'annonce';
    }

    /**
     * Button commands.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Créer')
                ->icon('pencil')
                ->method('createOrUpdate')
                ->canSee(!$this->websiteNews->exists),

            Button::make('Modifier')
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee($this->websiteNews->exists),

            Button::make('Supprimer')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->websiteNews->exists),
        ];
    }

    /**
     * Views.
     *
     * @throws \Throwable
     *
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            Layout::columns([
                Layout::rows([
                    Input::make('websiteNews.name')
                        ->title('Titre')
                        ->placeholder('Titre d\'annonce')
                        ->required(),
                    TextArea::make('websiteNews.content')
                        ->title('Texte a afficher')
                        ->required(),
                ]),
                Layout::rows([
                    Switcher::make('websiteNews.active')
                        ->sendTrueOrFalse()
                        ->title('Annonces visible')
                        ->value(true),

                    DateTimer::make('websiteNews.active_at')
                        ->enableTime(true)
                        ->title('Date de publication'),

                    DateTimer::make('websiteNews.end_at')
                        ->enableTime(true)
                        ->title('Date de fin'),


                ]),
            ]),
        ];
    }

    public function createOrUpdate(WebsiteNew $news, Request $request): \Illuminate\Http\RedirectResponse
    {
        $news->fill($request->get('websiteNews'))->save();

        Alert::info('L\'annonce a bien été crée');

        return redirect()->route('platform.annonces.list');
    }

    public function remove(WebsiteNew $new)
    {
        $new->delete();

        Alert::info('L\'annonce a bien été supprimer');

        return redirect()->route('platform.annonces.list');
    }

}

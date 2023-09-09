<?php

namespace App\Orchid\Screens\PageAdmin;

use App\Models\PageAdmin;
use App\Models\Programme;
use App\Models\GroupProgramme;
use Illuminate\Http\Request;
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

class PageAdminEditScreen extends Screen
{
    public PageAdmin $pageAdmin;

    /**
     * Query data.
     *
     * @return array
     */
    public function query(PageAdmin $pageAdmin): iterable
    {
        return [
            'pageAdmin'  => $pageAdmin
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->pageAdmin->exists ? 'Edition page' : 'Nouvelle page';
    }

    /**
     * Display header description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return 'Configuration et creation de page administrable';
    }

    /**
     * Button commands.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Creation page')
                ->icon('pencil')
                ->method('createOrUpdate')
                ->canSee(!$this->pageAdmin->exists),

            Button::make('Sauvegarder')
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee($this->pageAdmin->exists),

            Button::make('Supprimer')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->pageAdmin->exists),
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
            Layout::rows([
                Input::make('pageAdmin.name')
                    ->title('Nom')
                    ->placeholder('Titre page')
                    ->required(),

                Input::make('pageAdmin.path')
                    ->placeholder("Route d'access")
                    ->required()
                    ,

                Quill::make('pageAdmin.content')
                    ->title('Contenu')
                    ->placeholder('Contenu de la page')
                    ->required(),

            ])

        ];
    }

    /**
     * @param GroupProgramme    $programme
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOrUpdate(PageAdmin $pageAdmin, Request $request)
    {
        $pageAdmin->fill($request->get('pageAdmin'))->save();

        Alert::info('Page crée');

        return redirect()->route('platform.page-admin.list');
    }

    /**
     * @param GroupProgramme $programme
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function remove(PageAdmin $pageAdmin)
    {
        $pageAdmin->delete();

        Alert::info('Page supprimer');

        return redirect()->route('platform.page-admin.list');
    }

}

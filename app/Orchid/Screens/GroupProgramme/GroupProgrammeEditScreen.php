<?php

namespace App\Orchid\Screens\GroupProgramme;

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

class GroupProgrammeEditScreen extends Screen
{
    public GroupProgramme $programme;

    /**
     * Query data.
     *
     * @return array
     */
    public function query(GroupProgramme $programme): iterable
    {
        return [
            'programme'  => $programme
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->programme->exists ? 'Edition programme' : 'Nouveau programme';
    }

    /**
     * Display header description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return 'Configuration et creation de programme';
    }

    /**
     * Button commands.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Nouveau programme')
                ->icon('pencil')
                ->method('createOrUpdate')
                ->canSee(!$this->programme->exists),

            Button::make('Sauvegarder')
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee($this->programme->exists),

            Button::make('Supprimer')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->programme->exists),
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
                Input::make('programme.name')
                    ->title('Titre')
                    ->placeholder('Titre du groupe de Programme')
                    ->required(),

                Input::make('programme.height')
                    ->placeholder('Priorité de triage')
                    ->required()
                    ->type('number')
                    ->step(1),

                Quill::make('programme.description')
                    ->title('Description')
                    ->height('80vh')
                    ->placeholder('Description du groupe de programme')
                    ->required(),

                Cropper::make('programme.image')
                    ->height(800)
                    ->width(533)
                    ->targetUrl()
                    ->required(),

                Switcher::make('programme.active')
                    ->sendTrueOrFalse()
                    ->title('Groupe visible')
                    ->value(true)
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
    public function createOrUpdate(GroupProgramme $programme, Request $request)
    {
        $programme->fill($request->get('programme'))->save();

        Alert::info('Le groupe de programme a bien été crée');

        return redirect()->route('platform.group.programme.list');
    }

    /**
     * @param GroupProgramme $programme
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function remove(GroupProgramme $programme)
    {
        $programme->delete();

        Alert::info('Le groupe de programme a bien été supprimer');

        return redirect()->route('platform.group.programme.list');
    }

}

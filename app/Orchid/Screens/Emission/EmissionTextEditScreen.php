<?php

namespace App\Orchid\Screens\Emission;

use App\Models\Emision as Emission;
use App\Models\Programme;
use App\Models\Tag;
use App\Models\GroupProgramme;
use App\Orchid\Overrides\UploadOverRide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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


class EmissionTextEditScreen extends Screen
{
    public Emission $emission;

    /**
     * Query data.
     *
     * @return array
     */
    public function query(Emission $emission): iterable
    {
        return [
            'emission'  => $emission
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->emission->exists ? 'Edition article' : 'Nouvel article';
    }

    /**
     * Display header description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return 'Configuration et creation d\'emission';
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
                ->canSee(!$this->emission->exists),

            Button::make('Modifier')
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee($this->emission->exists),

            Button::make('Supprimer')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->emission->exists),
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
                    Input::make('emission.name')
                        ->title('Titre')
                        ->placeholder('Titre de l\'emission')
                        ->required(),
                    Relation::make('emission.programme_id')
                        ->fromModel(Programme::class, 'name')
                        ->applyScope('WithAuthPermissions')
                        ->chunk(1000)
                        ->searchColumns('name')
                        ->title('Choisir le programme')
                        ->required(),
                    Relation::make('emission.tags')
                        ->fromModel(Tag::class, 'name')
                        ->chunk(1000)
                        ->searchColumns('name->fr')
                        ->multiple()
                        ->title('Choisir les thèmes associées')
                        ->required(),
                ]),
                Layout::rows([
                    Switcher::make('emission.is_active')
                        ->sendTrueOrFalse()
                        ->title('Programme visible')
                        ->value(true),

                    DateTimer::make('emission.active_at')
                        ->enableTime(false)
                        ->title('Date de publication')
                        ->value(false)
                        ->required(),

                    Switcher::make('emission.is_put_forward')
                        ->sendTrueOrFalse()
                        ->title('Mettre a la une')
                        ->value(true),

                ]),
            ]),
            Layout::columns([
                Layout::rows([
                    Input::make('emission.media_type')
                                    ->hidden(true)
                                    ->value('text'),

                ]),
                Layout::rows([
                    Cropper::make('emission.image')
                        ->height(533)
                        ->width(800)
//                        @Todo Qualité retour papa!
//                        ->maxCanvas()
//                        ->minCanvas()
                        ->targetUrl()
                        ->storage('emission_image')
                        ->required(),
                ]),
            ]),
            Layout::columns([
                Layout::rows([
                    Quill::make('emission.description')
                        ->title('Description')
                        ->height('80vh')
                        ->placeholder('Description du programme')
                        ->required(),
                ]),
            ]),

        ];
    }

    /**
     * @param Emission    $emission
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOrUpdate(Emission $emission, Request $request)
    {
        $emission->fill($request->get('emission'));
        $emission->user_id = Auth::user()->id;
        $emission->media_type = 'text';

        $emission->saveOrFail();
        if (array_key_exists('tags', $request->get('emission')) && !empty($request->get('emission')['tags'])) {
            $tags = Tag::query();
            foreach ($request->get('emission')['tags'] as $tagId) {
                $tags->orWhere('id', '=', $tagId);
            }
            $emission->tags()->sync($tags->get()->pluck('id')->toArray());
        }

        $emission->saveOrFail();
        $emission->generateSlug();
        $emission->save();
        Alert::info('L\'emission a bien été crée');
        return redirect()->route('platform.emissions.list');
    }

    /**
     * @param Programme $programme
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function remove(Emission $emision)
    {
        $emision->delete();

        Alert::info('L\'emission a bien été supprimer');

        return redirect()->route('platform.emissions.list');
    }

}

<?php

namespace App\Orchid\Screens\Emission;

use App\Models\Emision as Emission;
use App\Models\Programme;
use App\Models\GroupProgramme;
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
use Spatie\Tags\Tag;

class EmissionEditScreen extends Screen
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
            'emission'  => $emission,
            'media'     => $emission->attachment('audio')->get()
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->emission->exists ? 'Edition emission' : 'Nouvelle emission';
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
            Layout::rows([
                Group::make([
                    Input::make('emission.title')
                        ->title('Titre')
                        ->placeholder('Titre de l\'emission')
                        ->required(),
                    Relation::make('emission.programme_id')
                        ->fromModel(Programme::class, 'name')
                        ->title('Choisir le programme')
                        ->required(),
                ]),

                Quill::make('emission.description')
                    ->title('Description')
                    ->placeholder('Description du programme')
                    ->required(),

                Cropper::make('emission.image')
                    ->height(800)
                    ->width(533)
                    ->targetUrl()
                    ->storage('emission_image')
                    ->required(),

                Group::make([
                    Switcher::make('emission.active')
                        ->sendTrueOrFalse()
                        ->title('Programme visible')
                        ->value(true),

                    DateTimer::make('emission.active_at')
                        ->enableTime(false)
                        ->title('Date de publication')
                        ->value(false),

                    Switcher::make('emission.is_put_forward')
                        ->sendTrueOrFalse()
                        ->title('Mettre a la une')
                        ->value(true),
                ]),

                Upload::make('media')
                    ->storage('emission_audio')
                    ->maxFiles(1)
                    ->groups('audio')
                    ->media()
                    ->acceptedFiles(env('FORMAT_AUDIO_ACCEPT')),

                Input::make('emission.media_type')
                    ->hidden(true)
                    ->value('audio'),

                Input::make('emission.user_id')
                    ->hidden(true)
                    ->value(Auth::user()->id),
            ])

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

        $emission->fill($request->get('emission'))
            ->save();
        $emission->attachment()->syncWithoutDetaching(
            $request->input('media', [])
        );

        Alert::info('Le programme a bien été crée');

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

        Alert::info('Le programme a bien été supprimer');

        return redirect()->route('platform.emissions.list');
    }

}

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


class EmissionVideoEditScreen extends Screen
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
            'media'     => $emission->attachment('video')->get(),
//            'duration'  => $emission->attachment('audio')->get()->first()->duration
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->emission->exists ? 'Edition emission video' : 'Nouvelle emission video';
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
                        ->title('Choisir le programme')
                        ->required(),
                    Relation::make('emission.tags')
                        ->fromModel(Tag::class, 'name')
                        ->multiple()
                        ->title('Choisir les tags associées')
                        ->required(),
                ]),
                Layout::rows([
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
            ]),
            Layout::columns([
                Layout::rows([
                    UploadOverRide::make('media')
                        ->storage('emission_video')
                        ->id('media-video')
                        ->maxFiles(1)
                        ->groups('video')
                        ->media()
                        ->acceptedFiles(env('FORMAT_VIDEO_ACCEPT'))
                    ,
//                    Upload::make('media')
//                        ->storage('emission_audio')
//                        ->id('media-audio')
//                        ->maxFiles(1)
//                        ->groups('audio')
//                        ->media()
//                        ->acceptedFiles(env('FORMAT_AUDIO_ACCEPT'))
//                    ,

                    Input::make('emission.duration')
                        ->type('number')
                        ->max(120)
                        ->min(0)
                        ->step(.01)
                        ->required()
                        ->title('Temps pour consulter en minutes')

                        ->pattern( '^[0-9]{1,3}(.[0-5])?([0-9])?$'),



                    Input::make('emission.media_type')
                                    ->hidden(true)
                                    ->value('video'),

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
                        ->height('450px')
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
        $emission->media_type = 'video';
        $emission->attachment()->sync(
            $request->input('media', [])
        );
        $emission->saveOrFail();
        if (array_key_exists('tags', $request->get('emission')) && !empty($request->get('emission')['tags'])) {
            $tags = Tag::query();
            foreach ($request->get('emission')['tags'] as $tagId) {
                $tags->orWhere('id', '=', $tagId);
            }
            $emission->tags()->sync($tags->get()->pluck('id')->toArray());
        }



//        $emission->attachment->first()->duration = (int)$request->get('duration');

//        $emission->attachment->first()->save();
        $emission->saveOrFail();
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

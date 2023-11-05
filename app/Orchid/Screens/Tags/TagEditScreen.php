<?php

namespace App\Orchid\Screens\Tags;

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

class TagEditScreen extends Screen
{
    public Tag $tag;

    /**
     * Query data.
     *
     * @return array
     */
    public function query(Tag $tag): iterable
    {
        return [
            'tag'  => $tag
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->tag->exists ? 'Thèmes / listes Editions' : 'Nouveau Thèmes / listes';
    }

    /**
     * Display header description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return 'Configuration des thèmes du site et des listes qui en résulte';
    }

    /**
     * Button commands.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Nouveau Thèmes')
                ->icon('pencil')
                ->method('createOrUpdate')
                ->canSee(!$this->tag->exists),

            Button::make('Sauvegarder')
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee($this->tag->exists),

            Button::make('Supprimer')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->tag->exists),
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
                Input::make('tag.name')
                    ->title('Thèmes')
                    ->help('Nom affiché.')
                    ->required(),

                Quill::make('tag.description')
                    ->title('Description')
                    ->height('80vh')
                    ->placeholder('Brief description')
                    ->required(),

                Input::make('tag.color')->type('color')
                    ->required(),

            ])

        ];
    }

    /**
     * @param Tag    $tag
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOrUpdate(Tag $tag, Request $request)
    {
        $tag->fill($request->get('tag'))->save();

        Alert::info('You have successfully created a Thèmes.');

        return redirect()->route('platform.tag.list');
    }

    /**
     * @param Tag $tag
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function remove(Tag $tag)
    {
        $tag->delete();

        Alert::info('You have successfully deleted the tag.');

        return redirect()->route('platform.tag.list');
    }

}

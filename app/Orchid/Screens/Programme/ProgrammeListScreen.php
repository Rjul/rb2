<?php

namespace App\Orchid\Screens\Programme;

use App\Models\Programme;
use App\Orchid\Layouts\Programme\ProgrammesListLayout;
use Orchid\Platform\Models\User;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Fields\DateRange;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Map;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\RadioButtons;
use Orchid\Screen\Fields\Range;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Fields\UTM;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Spatie\Tags\Tag;

class ProgrammeListScreen extends Screen
{
    /**
     * Query data.
     *
     */
    public function query()
    {
        $programmes_id = [];
        dump(Auth()->user()->permissions );
        foreach (Auth()->user()->permissions as $permision => $key) {
            if ($permision === "platform.programmes" && ($key === "1" || $key === true) ) {
                return [
                    'programmes' => Programme::filters()->defaultSort('id')->with('group_programme')->paginate()
                ];
            } else if (str_contains($permision, 'platform.emission.') && ($key === "1" || $key === true) ) {
                preg_match('/platform\.emission\.([0-9]+)/', $permision, $matches);
                dump($matches);
                $programmes_id[] = $matches[1];
            }
        }
        if (!empty($programmes_id)) {
            $query = Programme::filters()->defaultSort('id')->with('group_programme');
            foreach ($programmes_id as $id) {
                $query->orWhere('id', $id);
            };
            return [
                'programmes' => $query->paginate(),
            ];
        }
    }

    /**
     * The name is displayed on the user's screen and in the headers
     */
    public function name(): ?string
    {
        return 'Programme';
    }

    /**
     * The description is displayed on the user's screen under the heading
     */
    public function description(): ?string
    {
        return "Gestion des Programmes";
    }

    /**
     * Button commands.
     *
     * @return Link[]
     */
    public function commandBar(): array
    {
        return [
            Link::make('Nouveau Programme')
                ->icon('pencil')
                ->route('platform.programme.edit')
        ];
    }

    /**
     * Views.
     *
     * @return Layout[]
     */
    public function layout(): array
    {
        return [
            ProgrammesListLayout::class
        ];
    }
}

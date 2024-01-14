<?php

namespace App\Orchid\Screens\Emission;

use App\Models\Emision as Emission;
use App\Models\Programme;
use App\Orchid\Filters\ProgrammeFilter;
use App\Orchid\Layouts\Emissions\EmissionsListLayout;
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

class EmissionListScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'emissions' => Emission::query()
                ->withAuthPermissions()
                ->filters([ProgrammeFilter::class])
                ->defaultSort('active_at', 'DESC')
                ->paginate()
        ];
    }

    /**
     * The name is displayed on the user's screen and in the headers
     */
    public function name(): ?string
    {
        return 'Emissions';
    }

    public function with(): array
    {
        return ['programme'];
    }

    /**
     * The description is displayed on the user's screen under the heading
     */
    public function description(): ?string
    {
        return "Gestion des Emissions";
    }

    /**
     * Button commands.
     *
     * @return Link[]
     */
    public function commandBar(): array
    {
        return [

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
            EmissionsListLayout::class
        ];
    }
}

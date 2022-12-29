<?php
namespace App\Orchid\Actions;

use Illuminate\Support\Collection;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Toast;

class ApprovedAction extends \Orchid\Screen\Action
{
    /**
    * The button of the action.
    *
    * @return Button
    */
    public function button(): Button
    {
        return Button::make('Run Custom Action')->icon('fire');
    }

    /**
    * Perform the action on the given models.
    *
    * @param \Illuminate\Support\Collection $models
    */
    public function handle(Collection $models)
    {
        $models->each(function ($model) {
            $model->approuved = !$model->approuved;
        });

        Toast::message('It worked!');
    }
}


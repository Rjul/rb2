<?php

declare(strict_types=1);

namespace App\Orchid\Overrides;


use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Orchid\Screen\Field;
use Throwable;


class FieldOverRide extends Field
{


    /**
     * @throws Throwable
     *
     * @return Factory|View|mixed
     */
    public function render()
    {
        if (! $this->isSee()) {
            return;
        }

        $this
            ->checkRequired()
            ->modifyName()
            ->modifyValue()
            ->runBeforeRender()
            ->translate()
            ->checkError();

        $id = $this->getId();
        $this->set('id', $id);

        $errors = $this->getErrorsMessage();

        return view($this->view, array_merge($this->getAttributes(), [
            'attributes'     => $this->getAllowAttributes(),
            'dataAttributes' => $this->getAllowDataAttributes(),
            'id'             => $id,
            'old'            => $this->getOldValue(),
            'slug'           => $this->getSlug(),
            'oldName'        => $this->getOldName(),
            'typeForm'       => $this->typeForm ?? $this->vertical()->typeForm,
        ]))
            ->withErrors($errors);
    }

}

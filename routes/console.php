<?php

use FFMpeg\FFProbe;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('migrate:old_db', function () {
    $this->comment("Migrate old database");

    $this->comment('users');
    $old = \App\Models\OldModels\User::all();

    foreach ($old as $user) {
        $new = new \App\Models\User();
        $new->id = $user->id;
        $new->name = $user->name;
        $new->email = $user->email;
        $new->password = $user->password;
        $new->save();
    }

    $this->comment('groupsProgrammes');

    $old = \App\Models\OldModels\GroupProgramas::all();

    foreach ($old as $groupPrograma) {
        $new = new \App\Models\GroupProgramme();
        $new->id = $groupPrograma->idGrupo;
        $new->name = $groupPrograma->varNombreGrupo;
        $new->description = $groupPrograma->varDescripcion;
        $new->active = true;
        $new->save();
    }

    $this->comment('programmes');

    $old = \App\Models\OldModels\Programas::all();

    foreach ($old as $programa) {

        $new = new \App\Models\Programme();
        $new->id = $programa->idPrograma;
        $new->group_programme_id = $programa->idGrupo;
        $new->name = $programa->varNombrePrograma;
        $new->description = $programa->varDescripcion;
        $new->image = $programa->varImagen;
        $new->active = true;
        $new->save();
    }

    $old = \App\Models\OldModels\Emision::all();

    foreach ($old as $emision) {
        $new = new \App\Models\Emision();
        $new->id = $emision->id;
        $new->programme_id = $emision->idPrograma;
        $new->name = $emision->varTitle;
        $new->description = $emision->varDescripcion;

        // download image and create attachment and store absolute path
        if (file_exists('/storage/app/public/img/emisiones'.$emision->varImagen)) {

            \Illuminate\Support\Facades\Storage::disk('emission_image')
                ->put(
                    'old_image/'.$emision->varImagen,
                    file_get_contents('/storage/app/public/img/emisiones'.$emision->varImagen)
                );

            $file = new UploadedFile($path, $originalName);

            $attachment = (new File($file))->load();
        }


        $new->image = $emision->varImagen;
        $new->is_put_forward = $emision->flagAlaUne;
        $new->active_at = $emision->datFechaEmision;
        $new->active = true;
        $new->media_type = 'text';

        $new->save();
    }



})->purpose('Migrate old database');


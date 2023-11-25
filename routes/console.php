<?php

use FFMpeg\FFProbe;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\UploadedFile;
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
        if (\App\Models\User::find($user->id)) {
            continue;
        }
        $new = new \App\Models\User();
        $new->id = $user->id;
        $new->name = $user->name;
        $new->email = $user->email;
        $new->password = $user->password;
        $new->save();
    }

    $this->comment('groupsProgrammes');

    $old = \App\Models\OldModels\GroupProgramas::all();

    $count = 0;
    foreach ($old as $groupPrograma) {
        if (\App\Models\GroupProgramme::find($groupPrograma->idGrupo)) {
            continue;
        }
        $new = new \App\Models\GroupProgramme();
        $new->id = $groupPrograma->idGrupo;
        $new->name = $groupPrograma->varNombreGrupo;
        $new->description = $groupPrograma->varDescripcion;
        $new->height = $count;
        $new->active = true;
        $new->save();
        $count++;
    }

    $this->comment('programmes');

    $old = \App\Models\OldModels\Programas::all();

    $count = 0;
    foreach ($old as $programa) {
        if (\App\Models\Programme::find($programa->idPrograma)) {
            continue;
        }
        $new = new \App\Models\Programme();
        $new->id = $programa->idPrograma;
        $new->group_programme_id = $programa->idGrupo;
        $new->name = $programa->varNombrePrograma;
        $new->description = $programa->varDescripcion;
        $new->image = $programa->varImagen;
        $new->is_archived = false;
        $new->height = 0;

        $new->active = true;
        $new->save();
    }

    $old = \App\Models\OldModels\Emision::all();

    $count = 0;
    $numberFolder = 0;
    foreach ($old as $emision) {
        if (\App\Models\Emision::find($emision->id)) {
            continue;
        }
        $new = new \App\Models\Emision();
        $new->id = $emision->id;
        $new->programme_id = $emision->idPrograma;
        $new->name = $emision->varTitle;
        $new->description = $emision->varDescripcion;
        $new->image = '/storage/public/emission/images/old/'.$numberFolder.'/'.$emision->varImage;
        $new->is_put_forward = (bool)$emision->flagAlaUne;
        $new->active_at = $emision->datFechaEmision;
        $new->active = true;
        $new->media_type = 'audio';

        $new->save();

        // download image and create attachment and store absolute path
        if ($count > 200) {
            $numberFolder++;
            $count = 0;
        }
        dump(file_exists('https://www.radiobastides.fr/storage/audio/'.$emision->varUrlAudio));
        dump('https://www.radiobastides.fr/storage/audio/'.$emision->varUrlAudio);


        $path = storage_path('app/public/emission/audio/old/' . $numberFolder . '/');

        if (!file_exists($path)) {
            // Create the directory if it doesn't exist
            mkdir($path, 0755, true);
        }
        if (
            file_put_contents(
                $path . $emision->varUrlAudio,
                file_get_contents('https://www.radiobastides.fr/storage/audio/' . $emision->varUrlAudio
                )
            ) !== false
        ) {
            //   file_put_contents(/var/www/html/storage/app/public/emission/audios/old/0/4.mp3): Failed to open stream: No such file or directory

            $new->attachment()->create([
                'name' => basename($emision->varUrlAudio),
                'original_name' => $emision->varUrlAudio,
                'mime' => 'audio/*',
                'path' => 'old/' . $numberFolder . '/',
                'disk' => 'emission_audio',
                'group' => 'audio',
                'duration' => 0,
                'sort' => 0,
            ])->save();
            $new->save();
        }

        if ($emision->varImage == null) {
            continue;
        }
        $path = storage_path('app/public/emission/images/old/'.$numberFolder.'/');
        if (!file_exists($path)) {
            // Create the directory if it doesn't exist
            mkdir($path, 0755, true);
        }
        if (
            file_put_contents(
                $path . $emision->varImage,
                file_get_contents('https://radiobastides.fr/storage/img/emisiones/'.$emision->varImage)
            )
        ) {

            $new->attachment()->create([
                'name' => basename($emision->varImage),
                'original_name' => $emision->varImage,
                'mime' => 'image/*',
                'path' => 'old/'.$numberFolder.'/',
                'disk' => 'emission_image',
                'duration' => 0,
                'sort' => 0,
            ])->save();
            $new->save();
        }

    }

})->purpose('Migrate old database');


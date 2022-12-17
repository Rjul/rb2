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

Artisan::command('applied:duration', function () {
    $this->comment("Get all attachment without duration");
    $attachments = \App\Models\Attachment::query()->where('duration', null)->get();

    $this->comment(sprintf('%s found', count($attachments)));
    $attachments->each(function ($attachment) {
        $file =  __DIR__.'/../storage/app/public/emission/audio/'. $attachment->path . $attachment->name .'.'. $attachment->extension;
        $mp3 = new \App\Utilities\MP3File( __DIR__.'/../storage/app/public/emission/audio/'. $attachment->path . $attachment->name .'.'. $attachment->extension);
        dump($file);
        dump($mp3->getDuration() / 60);
        dump($mp3->getDurationEstimate() / 60);

        if (file_exists($file)){
            ## open and read video file
            $handle = fopen($file, "r");

            ## read video file size
            $contents = fread($handle, filesize($file));
            fclose($handle);
            $make_hexa = hexdec(bin2hex(substr($contents,strlen($contents)-3)));
            if (strlen($contents) > $make_hexa){
                $pre_duration = hexdec(bin2hex(substr($contents,strlen($contents)-$make_hexa,3))) ;
                dump($pre_duration);
                $post_duration = $pre_duration/1000;
                $timehours = $post_duration/3600;
                $timeminutes =($post_duration % 3600)/60;
                $timeseconds = ($post_duration % 3600) % 60;
                $timehours = explode(".", $timehours);
                $timeminutes = explode(".", $timeminutes);
                $timeseconds = explode(".", $timeseconds);
                $duration = $timehours[0]. ":" . $timeminutes[0]. ":" . $timeseconds[0];
            }
            $attachment->duration = $duration ?? null;
            dump($duration ?? null);
        } else {
            $attachment->duration = null;
            dump('nan');
        }
    });

})->purpose('Get all attachment without duration and try to get this');

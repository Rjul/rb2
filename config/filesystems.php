<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
                'url' => env('APP_URL').'/storage/public',
            'visibility' => 'public',
            'throw' => false,
        ],

        'emission_image' => [
            'driver' => 'local',
            'root' => storage_path('app/public/emission/images'),
            'url' => env('APP_URL').'/storage/public/emission/images',
            'visibility' => 'public',
            'throw' => false,
        ],

        'emission_audio' => [
            'driver' => 'local',
            'root' => storage_path('app/public/emission/audio'),
            'url' => env('APP_URL').'/storage/public/emission/audio',
            'visibility' => 'public',
            'throw' => false,
        ],

        'emission_video' => [
            'driver' => 'ftp',
            'host' => env('FTP_HOST'),
            'port' => env('FTP_PORT', 21),
            'username' => env('FTP_USERNAME'),
            'password' => env('FTP_PASSWORD'),
            'maxTries' => 4,
            'root' => env('SFTP_ROOT', '/'),
            'url' => env('STORAGE_URL'),
            'public' => true,
            'visibility' => 'public',
            'directory_visibility' => 'public',
            'timeout' => 30,
            'useAgent' => true,
            'throw' => true,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app'),
    ],

];

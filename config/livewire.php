<?php

return [

    /*
    |---------------------------------------------------------------------------
    | Class Namespace
    |---------------------------------------------------------------------------
    |
    | Racine des composants Livewire. En v3 le défaut est App\Livewire (nos
    | composants y sont : App\Livewire\Newsletter, App\Livewire\HomePage,
    | App\Livewire\V2\*). Ne PAS remettre l'ancienne valeur v2 App\Http\Livewire,
    | sinon Livewire ne trouve plus les composants (« Unable to find component »).
    |
    */

    'class_namespace' => 'App\\Livewire',

    /*
    |---------------------------------------------------------------------------
    | View Path
    |---------------------------------------------------------------------------
    */

    'view_path' => resource_path('views/livewire'),

    /*
    |---------------------------------------------------------------------------
    | Layout
    |---------------------------------------------------------------------------
    | Layout par défaut pour un composant rendu en pleine page sans layout
    | explicite. Nos pages v2 passent déjà ->layout('layouts.tall', ...).
    |
    */

    'layout' => 'components.layouts.app',

    /*
    |---------------------------------------------------------------------------
    | Lazy Loading Placeholder
    |---------------------------------------------------------------------------
    */

    'lazy_placeholder' => null,

    /*
    |---------------------------------------------------------------------------
    | Temporary File Uploads
    |---------------------------------------------------------------------------
    | Limite d'upload fixée à 200 Mo (max:204800 Ko).
    | ⚠️ PHP doit aussi autoriser 200 Mo côté serveur, sinon l'upload échoue
    | AVANT la validation Livewire : upload_max_filesize=200M, post_max_size=200M
    | (et max_execution_time / max_input_time suffisants pour les gros fichiers).
    |
    */

    'temporary_file_upload' => [
        'disk' => null,        // Ex : 'local', 's3'                 | Défaut : 'default'
        'rules' => ['required', 'file', 'max:204800'], // 200 Mo (204800 Ko)
        'directory' => null,   // Ex : 'tmp'                         | Défaut : 'livewire-tmp'
        'middleware' => null,  // Ex : 'throttle:5,1'                | Défaut : 'throttle:60,1'
        'preview_mimes' => [   // Types autorisés pour les URL de prévisualisation temporaires.
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4',
            'mov', 'avi', 'wmv', 'mp3', 'm4a',
            'jpg', 'jpeg', 'mpga', 'webp', 'wma',
        ],
        'max_upload_time' => 5, // Durée max (minutes) avant invalidation d'un upload.
        'cleanup' => true,      // Nettoie les uploads temporaires de plus de 24 h.
    ],

    /*
    |---------------------------------------------------------------------------
    | Render On Redirect
    |---------------------------------------------------------------------------
    */

    'render_on_redirect' => false,

    /*
    |---------------------------------------------------------------------------
    | Eloquent Model Binding
    |---------------------------------------------------------------------------
    */

    'legacy_model_binding' => false,

    /*
    |---------------------------------------------------------------------------
    | Auto-inject Frontend Assets
    |---------------------------------------------------------------------------
    */

    'inject_assets' => true,

    /*
    |---------------------------------------------------------------------------
    | Navigate (mode SPA — wire:navigate)
    |---------------------------------------------------------------------------
    | Le front v2 utilise wire:navigate (lecteur audio persistant). La barre de
    | progression reprend le navy de la marque.
    |
    */

    'navigate' => [
        'show_progress_bar' => true,
        'progress_bar_color' => '#001C41',
    ],

    /*
    |---------------------------------------------------------------------------
    | HTML Morph Markers
    |---------------------------------------------------------------------------
    */

    'inject_morph_markers' => true,

    /*
    |---------------------------------------------------------------------------
    | Smart Wire Keys
    |---------------------------------------------------------------------------
    */

    'smart_wire_keys' => false,

    /*
    |---------------------------------------------------------------------------
    | Pagination Theme
    |---------------------------------------------------------------------------
    | Tailwind : cohérent avec l'override crawlable
    | resources/views/vendor/livewire/tailwind.blade.php (liens <a href wire:navigate>).
    |
    */

    'pagination_theme' => 'tailwind',

    /*
    |---------------------------------------------------------------------------
    | Release Token
    |---------------------------------------------------------------------------
    */

    'release_token' => 'a',
];

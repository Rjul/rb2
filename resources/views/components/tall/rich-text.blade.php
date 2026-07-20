@props(['html' => null])

@php
    // Contenu administrable rendu en HTML : on assainit côté serveur (retire
    // <script>, on* handlers, iframes/URLs dangereuses) — défense en profondeur
    // même vis-à-vis d'éditeurs BO à privilèges restreints.
    $config = (new \Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig())
        ->allowSafeElements()
        ->allowRelativeLinks()
        ->allowRelativeMedias()
        ->forceHttpsUrls()
        // Le RichEditor (Filament/TipTap) porte le soulignement, l'alignement et
        // les couleurs en style inline (ex. text-decoration:underline). On autorise
        // donc `style` pour que ces mises en forme s'affichent sur le site.
        // Contenu BO de confiance ; script/on*/URLs dangereuses restent bloqués
        // par allowSafeElements() + forceHttpsUrls().
        ->allowAttribute('style', '*');

    $clean = trim((new \Symfony\Component\HtmlSanitizer\HtmlSanitizer($config))->sanitize((string) $html));
@endphp

@if ($clean !== '')
    <div {{ $attributes }}>{!! $clean !!}</div>
@endif

@props(['html' => null])

@php
    // Contenu administrable rendu en HTML : on assainit côté serveur (retire
    // <script>, on* handlers, iframes/URLs dangereuses) — défense en profondeur
    // même vis-à-vis d'éditeurs BO à privilèges restreints.
    $config = (new \Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig())
        ->allowSafeElements()
        ->allowRelativeLinks()
        ->allowRelativeMedias()
        ->forceHttpsUrls();

    $clean = trim((new \Symfony\Component\HtmlSanitizer\HtmlSanitizer($config))->sanitize((string) $html));
@endphp

@if ($clean !== '')
    <div {{ $attributes }}>{!! $clean !!}</div>
@endif
